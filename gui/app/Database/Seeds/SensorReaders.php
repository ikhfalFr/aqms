<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SensorReaders extends Seeder
{
	public function run()
	{
		$this->db->query("TRUNCATE TABLE sensor_readers");
		$data = [
			['driver' => 'fs2_membrasens_v4.py', 'sensor_code' => '/dev/ttyMEMBRASENSE1', 'baud_rate' => '19200', 'pins' => ''],
			['driver' => 'fs2_membrasens_v4.py', 'sensor_code' => '/dev/ttyMEMBRASENSE2', 'baud_rate' => '19200', 'pins' => ''],
		];
		$this->db->table('sensor_readers')->insertBatch($data);
	}
}
