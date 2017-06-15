<?php

namespace FMADM\Topsis;

class Fuzzy
{
	/**
	 * @var FuzzyProcessorInterface
	 */
	private $processor;

	/**
	 * @var array
	 */
	private $criterionName;

	public function __construct(FuzzyProcessorInterface $processor)
	{
		$this->processor = $processor;
	}

	public function normalizeCriterionData($data)
	{
		if (!is_array($data) || empty($data)) {
			throw new \InvalidArgumentException(
				sprintf("Criterion data on %s must be an non-empty array.", __METHOD__)
			);
		}

		$names = $this->processor->getCriterionNames();

		foreach ($data as $mKey => $mValue) {
			foreach ($mValue as $cKey => $cValue) {
				$data[$mKey][$cKey] = $this->processor->find($cValue, $names[$cKey]);
			}
		}

		return $data;
	}
}