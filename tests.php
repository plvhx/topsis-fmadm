<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

$data = [
	[0.5290, 0.7742, 1, 1, 0.7665],
	[0.4110, 0.1275, 1, 1, 0.7415],
	[0.7050, 0.7537, 0.9400, 1, 0.8080],
	[0.3520, 0.2438, 0.8400, 1, 0.8330],
	[0.5290, 0.7065, 0.9200, 0.9600, 0.8080],
	[0.2940, 0.1970, 0.8000, 1, 0.8330],
	[0.4110, 0.4054, 0.8200, 1, 0.8330],
	[0.4700, 0.5043, 0.9000, 0.9600, 0.8750],
	[0.7640, 1, 1, 1, 0.8750],
	[0.9410, 1, 1, 1, 0.8330]
];

$abstract = [
	['IIIA', 7742, 56, 26, 15.33],
	['IIC', 1275, 53, 25, 14.83],
	['IIID', 7537, 57, 26, 16.16],
	['IIB', 2438, 42, 25, 16.66],
	['IIIA', 7065, 46, 24, 16.16],
	['IIA', 1970, 40, 25.5, 16.66],
	['IIC', 4054, 41, 26.5, 16.66],
	['IID', 5043, 45, 24, 17.5],
	['IVA', 10432, 57, 25.5, 17.5],
	['IVD', 10743, 58, 23.5, 16.66],
];

$processor = (new \FMADM\Topsis\FuzzyProcessor)
	->withCriterion('golongan', ['IA', 'IB', 'IC', 'ID', 'IIA', 'IIB', 'IIC', 'IID', 'IIIA', 'IIIB', 'IIIC', 'IIID', 'IVA', 'IVB', 'IVC', 'IVD', 'IVE'])
	->withCriterion('lama-mengajar', ['lower-bound' => 0, 'upper-bound' => 10000])
	->withCriterion('usia', ['lower-bound' => 20, 'upper-bound' => 60])
	->withCriterion('poin-kedisiplinan-avg', ['lower-bound' => 5, 'upper-bound' => 25])
	->withCriterion('poin-attitude-avg', ['lower-bound' => 5, 'upper-bound' => 20]);
$fuzzy = new \FMADM\Topsis\Fuzzy($processor);
$handler = new \FMADM\Topsis\Topsis($abstract);
$handler->needsFuzzifiedData($fuzzy);
$handler->setWeight([1, 0.5, 0.75, 0.5, 1]);
$handler->normalizeCriterionMatrix();
$pref = $handler->getPreferenceValuePerAlternative();

print_r($pref);