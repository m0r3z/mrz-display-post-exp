<?php
/**
 * Moteur de templates pour le rendu des items.
 *
 * Placeholders supportés :
 *   - {post_title}, {post_url}, {post_excerpt}, {post_excerpt:N} (tronqué à N mots)
 *   - {post_thumbnail} (balise <img>), {post_thumbnail_url}
 *   - {%nom_champ_acf%}
 *   - {acf_date:champ} ou {acf_date:champ:F} (date ACF en spans jour/mois/année ; mois = m|n|F|M)
 *   - {taxonomy:slug} (chaque terme dans un <span class="mrz-dpe-term">, sans séparateur)
 *   - {taxonomy:slug:first} (premier terme, texte brut)
 *
 * Conditionnels :
 *   - {#if %champ_acf%}...{/if}
 *   - {#if post_title}...{/if}
 *
 * Aucune exécution de code : pas d'eval/extract. Échappement par type à la substitution.
 */

namespace Mrzdpe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TemplateParser {

	const MAX_RECURSION = 5;

	/**
	 * Cache ACF par post_id (in-memory, scope requête).
	 *
	 * @var array
	 */
	private static $acf_cache = array();

	public static function render( $template, $post_id ) {
		$template = (string) $template;
		if ( '' === $template || ! $post_id ) {
			return '';
		}

		$post_id = (int) $post_id;

		// Passe 1 : conditionnels (résolus récursivement sur max 5 niveaux).
		$out = self::resolve_conditionals( $template, $post_id, 0 );

		// Passe 2 : placeholders simples.
		$out = self::resolve_placeholders( $out, $post_id );

		return $out;
	}

	private static function resolve_conditionals( $template, $post_id, $depth ) {
		if ( $depth >= self::MAX_RECURSION ) {
			// Retire tous les marqueurs restants pour ne pas les laisser fuiter.
			return preg_replace( '/\{#if\s+[^}]+\}|\{\/if\}/', '', $template );
		}

		$pattern = '/\{#if\s+(%?)([\w:-]+)\1\}((?:(?!\{#if\b|\{\/if\}).)*)\{\/if\}/s';

		return preg_replace_callback(
			$pattern,
			function ( $m ) use ( $post_id, $depth ) {
				$is_acf = ( '%' === $m[1] );
				$key    = $m[2];
				$body   = $m[3];

				$value = $is_acf
					? self::get_acf_raw( $key, $post_id )
					: self::get_native_raw( $key, $post_id );

				if ( ! self::is_truthy( $value ) ) {
					return '';
				}

				// Résolution récursive pour conditionnels imbriqués.
				return self::resolve_conditionals( $body, $post_id, $depth + 1 );
			},
			$template
		);
	}

	private static function resolve_placeholders( $template, $post_id ) {
		// ACF : {%field_name%}
		$template = preg_replace_callback(
			'/\{%([\w-]+)%\}/',
			function ( $m ) use ( $post_id ) {
				return self::escape_acf( $m[1], $post_id );
			},
			$template
		);

		// Champ ACF de date découpé en spans : {acf_date:champ} ou {acf_date:champ:F}
		// Jour (d) et année (Y) fixes ; le mois suit le token optionnel (m|n|F|M).
		$template = preg_replace_callback(
			'/\{acf_date:([\w-]+)(?::([a-zA-Z]+))?\}/',
			function ( $m ) use ( $post_id ) {
				$month_fmt = ( isset( $m[2] ) && in_array( $m[2], array( 'm', 'n', 'F', 'M' ), true ) ) ? $m[2] : 'm';
				return self::render_acf_date( $m[1], $post_id, $month_fmt );
			},
			$template
		);

		// Extrait tronqué à N mots : {post_excerpt:20}
		$template = preg_replace_callback(
			'/\{post_excerpt:(\d+)\}/',
			function ( $m ) use ( $post_id ) {
				$words = max( 1, (int) $m[1] );
				$text  = wp_strip_all_tags( get_the_excerpt( $post_id ) );
				return esc_html( wp_trim_words( $text, $words, '…' ) );
			},
			$template
		);

		// Taxonomies : {taxonomy:slug} ou {taxonomy:slug:first}
		$template = preg_replace_callback(
			'/\{taxonomy:([\w-]+)(?::(first))?\}/',
			function ( $m ) use ( $post_id ) {
				$terms = get_the_terms( $post_id, $m[1] );
				if ( empty( $terms ) || is_wp_error( $terms ) ) {
					return '';
				}
				if ( isset( $m[2] ) && 'first' === $m[2] ) {
					return esc_html( $terms[0]->name );
				}
				// Chaque terme dans son propre <span> (aucun séparateur imposé) :
				// l'espacement / les puces se gèrent en CSS via .mrz-dpe-term.
				$spans = array();
				foreach ( $terms as $term ) {
					$spans[] = '<span class="mrz-dpe-term">' . esc_html( $term->name ) . '</span>';
				}
				return implode( ' ', $spans );
			},
			$template
		);

		// Placeholders natifs WP.
		$native = array(
			'{post_title}'         => esc_html( get_the_title( $post_id ) ),
			'{post_url}'           => esc_url( get_permalink( $post_id ) ),
			'{post_excerpt}'       => esc_html( wp_strip_all_tags( get_the_excerpt( $post_id ) ) ),
			'{post_thumbnail}'     => wp_kses_post( get_the_post_thumbnail( $post_id, 'medium' ) ),
			'{post_thumbnail_url}' => esc_url( (string) get_the_post_thumbnail_url( $post_id, 'medium' ) ),
			'{post_id}'            => (string) $post_id,
		);

		return strtr( $template, $native );
	}

	/**
	 * Rend un champ ACF de date découpé en spans jour / mois / année.
	 * $month_fmt : token date() pour le span du mois (m|n|F|M), validé en amont.
	 */
	private static function render_acf_date( $field, $post_id, $month_fmt ) {
		$raw = function_exists( 'get_field' ) ? get_field( $field, $post_id, false ) : null;
		if ( ! is_string( $raw ) && ! is_numeric( $raw ) ) {
			return '';
		}
		$raw = (string) $raw;
		if ( '' === $raw ) {
			return '';
		}
		// Les champs date ACF stockent Ymd (date) ou Y-m-d H:i:s (date/heure) ;
		// strtotime gère les deux.
		$ts = strtotime( $raw );
		if ( false === $ts ) {
			return '';
		}
		return '<span class="mrz-dpe-month">' . esc_html( date_i18n( $month_fmt, $ts ) ) . '</span>'
			. '<span class="mrz-dpe-day">' . esc_html( date_i18n( 'd', $ts ) ) . '</span>'
			. '<span class="mrz-dpe-year">' . esc_html( date_i18n( 'Y', $ts ) ) . '</span>';
	}

	private static function get_native_raw( $key, $post_id ) {
		switch ( $key ) {
			case 'post_title':
				return get_the_title( $post_id );
			case 'post_url':
				return get_permalink( $post_id );
			case 'post_excerpt':
				return wp_strip_all_tags( get_the_excerpt( $post_id ) );
			case 'post_thumbnail':
			case 'post_thumbnail_url':
				return (string) get_the_post_thumbnail_url( $post_id, 'medium' );
			default:
				// Taxonomy checks via taxonomy:slug
				if ( 0 === strpos( $key, 'taxonomy:' ) ) {
					$parts = explode( ':', $key );
					$terms = get_the_terms( $post_id, $parts[1] );
					return ( ! empty( $terms ) && ! is_wp_error( $terms ) );
				}
				return '';
		}
	}

	private static function get_acf_raw( $name, $post_id ) {
		if ( isset( self::$acf_cache[ $post_id ][ $name ] ) ) {
			return self::$acf_cache[ $post_id ][ $name ]['raw'];
		}
		$value = function_exists( 'get_field' ) ? get_field( $name, $post_id ) : null;
		self::$acf_cache[ $post_id ][ $name ] = array( 'raw' => $value );
		return $value;
	}

	private static function escape_acf( $name, $post_id ) {
		$raw = self::get_acf_raw( $name, $post_id );

		if ( null === $raw || '' === $raw || array() === $raw ) {
			return '';
		}

		// Détection du type + choices du champ.
		$type    = 'text';
		$choices = array();
		if ( function_exists( 'acf_get_field' ) ) {
			$obj = acf_get_field( $name );
			if ( is_array( $obj ) ) {
				if ( isset( $obj['type'] ) ) {
					$type = (string) $obj['type'];
				}
				if ( isset( $obj['choices'] ) && is_array( $obj['choices'] ) ) {
					$choices = $obj['choices'];
				}
			}
		}

		// Cas champs structurés avec clés attendues (image, file, link).
		if ( is_array( $raw ) && ( isset( $raw['address'] ) || isset( $raw['url'] ) ) ) {
			$raw = isset( $raw['address'] ) ? $raw['address'] : $raw['url'];
		}

		// Cas tableau de valeurs multiples (checkbox, select multi, relationship).
		if ( is_array( $raw ) ) {
			$parts = array();
			foreach ( $raw as $item ) {
				// Return_format "Both (Array)" : chaque entrée = ['value'=>…, 'label'=>…].
				if ( is_array( $item ) ) {
					if ( isset( $item['label'] ) ) {
						$parts[] = (string) $item['label'];
					} elseif ( isset( $item['name'] ) ) {
						$parts[] = (string) $item['name'];
					} elseif ( isset( $item['ID'] ) ) {
						$title = get_the_title( (int) $item['ID'] );
						if ( '' !== $title ) {
							$parts[] = $title;
						}
					}
				} elseif ( is_scalar( $item ) ) {
					$item_str = (string) $item;
					$parts[]  = isset( $choices[ $item_str ] ) ? (string) $choices[ $item_str ] : $item_str;
				} elseif ( is_object( $item ) && isset( $item->ID ) ) {
					$title = get_the_title( (int) $item->ID );
					if ( '' !== $title ) {
						$parts[] = $title;
					}
				}
			}
			$raw = implode( ', ', $parts );
		} elseif ( is_scalar( $raw ) && isset( $choices[ (string) $raw ] ) ) {
			// Select/radio simple : remplace la clé par le label.
			$raw = (string) $choices[ (string) $raw ];
		}

		$value = (string) $raw;
		$value = apply_filters( 'mrzdpe_template_value', $value, $name, $post_id, $type );

		switch ( $type ) {
			case 'url':
			case 'link':
				return esc_url( $value );
			case 'wysiwyg':
				return wp_kses_post( $value );
			case 'image':
			case 'file':
				return esc_url( $value );
			default:
				return esc_html( $value );
		}
	}

	private static function is_truthy( $value ) {
		if ( null === $value ) {
			return false;
		}
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_array( $value ) ) {
			return ! empty( $value );
		}
		$str = (string) $value;
		return ( '' !== $str && '0' !== $str );
	}
}
