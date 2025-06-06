<?php // phpcs:ignore - @generation-checksum FR-123-8894
/**
 * Country Class for France (FR).
 *
 * State/province count: 123
 * City count: 8894
 * City count per state/province:
 * - 20R: 51 cities
 * - ARA: 1231 cities
 * - NAQ: 968 cities
 * - HDF: 921 cities
 * - GES: 887 cities
 * - OCC: 828 cities
 * - IDF: 694 cities
 * - BRE: 666 cities
 * - PDL: 660 cities
 * - PAC: 535 cities
 * - NOR: 526 cities
 * - CVL: 476 cities
 * - BFC: 451 cities
 *
 * @package WP_Ultimo\Country
 * @since 2.0.11
 */

namespace WP_Ultimo\Country;

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Country Class for France (FR).
 *
 * IMPORTANT:
 * This file is generated by build scripts, do not
 * change it directly or your changes will be LOST!
 *
 * @since 2.0.11
 *
 * @property-read string $code
 * @property-read string $currency
 * @property-read int $phone_code
 */
class Country_FR extends Country {

	use \WP_Ultimo\Traits\Singleton;

	/**
	 * General country attributes.
	 *
	 * This might be useful, might be not.
	 * In case of doubt, keep it.
	 *
	 * @since 2.0.11
	 * @var array
	 */
	protected $attributes = array(
		'country_code' => 'FR',
		'currency'     => 'EUR',
		'phone_code'   => 33,
	);

	/**
	 * The type of nomenclature used to refer to the country sub-divisions.
	 *
	 * @since 2.0.11
	 * @var string
	 */
	protected $state_type = 'region';

	/**
	 * Return the country name.
	 *
	 * @since 2.0.11
	 * @return string
	 */
	public function get_name() {

		return __('France', 'wp-ultimo-locations');

	} // end get_name;

	/**
	 * Returns the list of states for FR.
	 *
	 * @since 2.0.11
	 * @return array The list of state/provinces for the country.
	 */
	protected function states() {

		return array(
			'10'  => __('Aube', 'wp-ultimo-locations'),
			'11'  => __('Aude', 'wp-ultimo-locations'),
			'12'  => __('Aveyron', 'wp-ultimo-locations'),
			'13'  => __('Bouches-du-Rhône', 'wp-ultimo-locations'),
			'14'  => __('Calvados', 'wp-ultimo-locations'),
			'15'  => __('Cantal', 'wp-ultimo-locations'),
			'16'  => __('Charente', 'wp-ultimo-locations'),
			'17'  => __('Charente-Maritime', 'wp-ultimo-locations'),
			'18'  => __('Cher', 'wp-ultimo-locations'),
			'19'  => __('Corrèze', 'wp-ultimo-locations'),
			'21'  => __("Côte-d'Or", 'wp-ultimo-locations'),
			'22'  => __("Côtes-d'Armor", 'wp-ultimo-locations'),
			'23'  => __('Creuse', 'wp-ultimo-locations'),
			'24'  => __('Dordogne', 'wp-ultimo-locations'),
			'25'  => __('Doubs', 'wp-ultimo-locations'),
			'26'  => __('Drôme', 'wp-ultimo-locations'),
			'27'  => __('Eure', 'wp-ultimo-locations'),
			'28'  => __('Eure-et-Loir', 'wp-ultimo-locations'),
			'29'  => __('Finistère', 'wp-ultimo-locations'),
			'30'  => __('Gard', 'wp-ultimo-locations'),
			'31'  => __('Haute-Garonne', 'wp-ultimo-locations'),
			'32'  => __('Gers', 'wp-ultimo-locations'),
			'33'  => __('Gironde', 'wp-ultimo-locations'),
			'34'  => __('Hérault', 'wp-ultimo-locations'),
			'35'  => __('Ille-et-Vilaine', 'wp-ultimo-locations'),
			'36'  => __('Indre', 'wp-ultimo-locations'),
			'37'  => __('Indre-et-Loire', 'wp-ultimo-locations'),
			'38'  => __('Isère', 'wp-ultimo-locations'),
			'39'  => __('Jura', 'wp-ultimo-locations'),
			'40'  => __('Landes', 'wp-ultimo-locations'),
			'41'  => __('Loir-et-Cher', 'wp-ultimo-locations'),
			'42'  => __('Loire', 'wp-ultimo-locations'),
			'43'  => __('Haute-Loire', 'wp-ultimo-locations'),
			'44'  => __('Loire-Atlantique', 'wp-ultimo-locations'),
			'45'  => __('Loiret', 'wp-ultimo-locations'),
			'46'  => __('Lot', 'wp-ultimo-locations'),
			'47'  => __('Lot-et-Garonne', 'wp-ultimo-locations'),
			'48'  => __('Lozère', 'wp-ultimo-locations'),
			'49'  => __('Maine-et-Loire', 'wp-ultimo-locations'),
			'50'  => __('Manche', 'wp-ultimo-locations'),
			'51'  => __('Marne', 'wp-ultimo-locations'),
			'52'  => __('Haute-Marne', 'wp-ultimo-locations'),
			'53'  => __('Mayenne', 'wp-ultimo-locations'),
			'54'  => __('Meurthe-et-Moselle', 'wp-ultimo-locations'),
			'55'  => __('Meuse', 'wp-ultimo-locations'),
			'56'  => __('Morbihan', 'wp-ultimo-locations'),
			'57'  => __('Moselle', 'wp-ultimo-locations'),
			'58'  => __('Nièvre', 'wp-ultimo-locations'),
			'59'  => __('Nord', 'wp-ultimo-locations'),
			'60'  => __('Oise', 'wp-ultimo-locations'),
			'61'  => __('Orne', 'wp-ultimo-locations'),
			'62'  => __('Pas-de-Calais', 'wp-ultimo-locations'),
			'63'  => __('Puy-de-Dôme', 'wp-ultimo-locations'),
			'64'  => __('Pyrénées-Atlantiques', 'wp-ultimo-locations'),
			'65'  => __('Hautes-Pyrénées', 'wp-ultimo-locations'),
			'66'  => __('Pyrénées-Orientales', 'wp-ultimo-locations'),
			'67'  => __('Bas-Rhin', 'wp-ultimo-locations'),
			'68'  => __('Haut-Rhin', 'wp-ultimo-locations'),
			'69'  => __('Rhône', 'wp-ultimo-locations'),
			'70'  => __('Haute-Saône', 'wp-ultimo-locations'),
			'71'  => __('Saône-et-Loire', 'wp-ultimo-locations'),
			'72'  => __('Sarthe', 'wp-ultimo-locations'),
			'73'  => __('Savoie', 'wp-ultimo-locations'),
			'74'  => __('Haute-Savoie', 'wp-ultimo-locations'),
			'76'  => __('Seine-Maritime', 'wp-ultimo-locations'),
			'77'  => __('Seine-et-Marne', 'wp-ultimo-locations'),
			'78'  => __('Yvelines', 'wp-ultimo-locations'),
			'79'  => __('Deux-Sèvres', 'wp-ultimo-locations'),
			'80'  => __('Somme', 'wp-ultimo-locations'),
			'81'  => __('Tarn', 'wp-ultimo-locations'),
			'82'  => __('Tarn-et-Garonne', 'wp-ultimo-locations'),
			'83'  => __('Var', 'wp-ultimo-locations'),
			'84'  => __('Vaucluse', 'wp-ultimo-locations'),
			'85'  => __('Vendée', 'wp-ultimo-locations'),
			'86'  => __('Vienne', 'wp-ultimo-locations'),
			'87'  => __('Haute-Vienne', 'wp-ultimo-locations'),
			'88'  => __('Vosges', 'wp-ultimo-locations'),
			'89'  => __('Yonne', 'wp-ultimo-locations'),
			'90'  => __('Territoire de Belfort', 'wp-ultimo-locations'),
			'91'  => __('Essonne', 'wp-ultimo-locations'),
			'92'  => __('Hauts-de-Seine', 'wp-ultimo-locations'),
			'93'  => __('Seine-Saint-Denis', 'wp-ultimo-locations'),
			'94'  => __('Val-de-Marne', 'wp-ultimo-locations'),
			'95'  => __("Val-d'Oise", 'wp-ultimo-locations'),
			'971' => __('Guadeloupe', 'wp-ultimo-locations'),
			'972' => __('Martinique', 'wp-ultimo-locations'),
			'973' => __('French Guiana', 'wp-ultimo-locations'),
			'974' => __('La Réunion', 'wp-ultimo-locations'),
			'976' => __('Mayotte', 'wp-ultimo-locations'),
			'01'  => __('Ain', 'wp-ultimo-locations'),
			'02'  => __('Aisne', 'wp-ultimo-locations'),
			'03'  => __('Allier', 'wp-ultimo-locations'),
			'06'  => __('Alpes-Maritimes', 'wp-ultimo-locations'),
			'04'  => __('Alpes-de-Haute-Provence', 'wp-ultimo-locations'),
			'6AE' => __('Alsace', 'wp-ultimo-locations'),
			'08'  => __('Ardennes', 'wp-ultimo-locations'),
			'07'  => __('Ardèche', 'wp-ultimo-locations'),
			'09'  => __('Ariège', 'wp-ultimo-locations'),
			'ARA' => __('Auvergne-Rhône-Alpes', 'wp-ultimo-locations'),
			'BFC' => __('Bourgogne-Franche-Comté', 'wp-ultimo-locations'),
			'BRE' => __('Bretagne', 'wp-ultimo-locations'),
			'CVL' => __('Centre-Val de Loire', 'wp-ultimo-locations'),
			'CP'  => __('Clipperton', 'wp-ultimo-locations'),
			'20R' => __('Corse', 'wp-ultimo-locations'),
			'2A'  => __('Corse-du-Sud', 'wp-ultimo-locations'),
			'PF'  => __('French Polynesia', 'wp-ultimo-locations'),
			'TF'  => __('French Southern and Antarctic Lands', 'wp-ultimo-locations'),
			'GES' => __('Grand-Est', 'wp-ultimo-locations'),
			'2B'  => __('Haute-Corse', 'wp-ultimo-locations'),
			'05'  => __('Hautes-Alpes', 'wp-ultimo-locations'),
			'HDF' => __('Hauts-de-France', 'wp-ultimo-locations'),
			'69M' => __('Métropole de Lyon', 'wp-ultimo-locations'),
			'NOR' => __('Normandie', 'wp-ultimo-locations'),
			'NAQ' => __('Nouvelle-Aquitaine', 'wp-ultimo-locations'),
			'OCC' => __('Occitanie', 'wp-ultimo-locations'),
			'75C' => __('Paris', 'wp-ultimo-locations'),
			'PDL' => __('Pays-de-la-Loire', 'wp-ultimo-locations'),
			'PAC' => __('Provence-Alpes-Côte-d’Azur', 'wp-ultimo-locations'),
			'PM'  => __('Saint Pierre and Miquelon', 'wp-ultimo-locations'),
			'BL'  => __('Saint-Barthélemy', 'wp-ultimo-locations'),
			'MF'  => __('Saint-Martin', 'wp-ultimo-locations'),
			'WF'  => __('Wallis and Futuna', 'wp-ultimo-locations'),
			'IDF' => __('Île-de-France', 'wp-ultimo-locations'),
		);

	} // end states;

} // end class Country_FR;
