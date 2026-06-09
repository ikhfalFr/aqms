<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Parameters extends Seeder
{
	public function run()
	{
		$this->db->query("TRUNCATE TABLE parameters");
		$data = [
			['p_type' => 'gas', 'code' => 'no2', 'caption_id' => 'NO<sub>2</sub>', 'caption_en' => 'NO<sub>2</sub>',	'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '46.01', 	'formula' => 'round((62.5 * $sensor[1][0]) - 0.02,2)', 'is_view' => '1', 'is_graph' => '1'],
			['p_type' => 'gas', 'code' => 'o3', 'caption_id' => 'O<sub>3</sub>', 'caption_en' => 'O<sub>3</sub>', 		'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '48', 		'formula' => 'round((62.5 * $sensor[1][1]) - 0.02,2)', 'is_view' => '1', 'is_graph' => '1'],
			['p_type' => 'gas', 'code' => 'co', 'caption_id' => 'CO', 'caption_en' => 'CO', 							'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '28.01', 	'formula' => 'round((62.5 * $sensor[1][2]) - 0.02,2)', 'is_view' => '1', 'is_graph' => '1'],
			['p_type' => 'gas', 'code' => 'so2', 'caption_id' => 'SO<sub>2</sub>', 'caption_en' => 'SO<sub>2</sub>', 	'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '64.06', 	'formula' => 'round((62.5 * $sensor[1][3]) - 0.02,2)', 'is_view' => '1', 'is_graph' => '1'],
			['p_type' => 'gas', 'code' => 'hc', 'caption_id' => 'HC', 'caption_en' => 'HC', 							'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '13.0186', 	'formula' => 'round((62.5 * $sensor[2][0]) - 0.02,2)', 'is_view' => '1', 'is_graph' => '1'],
			['p_type' => 'gas', 'code' => 'h2s', 'caption_id' => 'H<sub>2</sub>S', 'caption_en' => 'H<sub>2</sub>S', 	'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '34.08', 	'formula' => '', 'is_view' => '0', 'is_graph' => '0'],
			['p_type' => 'gas', 'code' => 'cs2', 'caption_id' => 'CS<sub>2</sub>', 'caption_en' => 'CS<sub>2</sub>', 	'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '76.1407', 	'formula' => '', 'is_view' => '0', 'is_graph' => '0'],
			['p_type' => 'gas', 'code' => 'nh3', 'caption_id' => 'NH<sub>3</sub>', 'caption_en' => 'NH<sub>3</sub>', 	'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '76.1407', 	'formula' => '', 'is_view' => '0', 'is_graph' => '0'],
			['p_type' => 'gas', 'code' => 'ch4', 'caption_id' => 'CH<sub>4</sub>', 'caption_en' => 'CH<sub>4</sub>', 	'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '16.04', 	'formula' => '', 'is_view' => '0', 'is_graph' => '0'],
			['p_type' => 'gas', 'code' => 'voc', 'caption_id' => 'VOC', 'caption_en' => 'VOC', 						'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '78.9516', 	'formula' => '', 'is_view' => '0', 'is_graph' => '0'],
			['p_type' => 'gas', 'code' => 'nmhc', 'caption_id' => 'NMHC', 'caption_en' => 'NMHC', 						'default_unit' => 'µg/m<sup>3</sup>', 'molecular_mass' => '110', 		'formula' => '', 'is_view' => '0', 'is_graph' => '0'],
		];
		$this->db->table('parameters')->insertBatch($data);
	}
}
