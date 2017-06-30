<?php

namespace FMADM\Topsis;

class Topsis
{
	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var array
	 */
	private $weight;

	/**
	 * @var array
	 */
	private $debug = [];

	/**
	 * @var array
	 */
	private $countWorkingAlternative;

	/**
	 * @var bool
	 */
	private $isDebugMode;

	public function __construct($data, $needDebug = false)
	{
		$this->data = $data;
		$this->isDebugMode = $needDebug;
		$this->countWorkingAlternative = $this->measureWorkingAlternative();

		if ($this->isDebugMode) {
			$this->debug['working_rank_per_alternative'] = $this->countWorkingAlternative;
		}
	}

	public function needsFuzzifiedData(Fuzzy $fuzzy)
	{
		$this->data = $fuzzy->normalizeCriterionData($this->data);
		$this->countWorkingAlternative = $this->measureWorkingAlternative();

		if ($this->isDebugMode) {
			$this->debug['working_rank_per_alternative'] = $this->countWorkingAlternative;
		}
	}

	public function setWeight($weight)
	{
		$this->weight = $weight;

		if ($this->isDebugMode) {
			$this->debug['weight'] = $this->weight;
		}
	}

	protected function measureWorkingAlternative()
	{
		$count = [];

		foreach ($this->data as $mKey => $mValue) {
			foreach ($mValue as $cKey => $cValue) {
				$count[$cKey] = (isset($count[$cKey])
					? $count[$cKey] + pow($cValue, 2)
					: pow($cValue, 2));
			}
		}

		return $count;
	}

	public function normalizeCriterionMatrix()
	{
		foreach ($this->data as $mKey => $mValue) {
			foreach ($mValue as $cKey => $cValue) {
				$this->data[$mKey][$cKey] =
					($cValue / $this->countWorkingAlternative[$cKey]) * $this->weight[$cKey];
			}
		}
	}

	protected function getPositiveSolution()
	{
		$max = [];

		foreach ($this->data as $mKey => $mValue) {
			foreach ($mValue as $cKey => $cValue) {
				$max[$cKey] = (isset($max[$cKey])
					? ($max[$cKey] > $cValue ? $max[$cKey] : $cValue)
					: $cValue);
			}
		}

		if ($this->isDebugMode) {
			$this->debug['positive_ideal_solution'] = $max;
		}

		return $max;
	}

	protected function getNegativeSolution()
	{
		$min = [];

		foreach ($this->data as $mKey => $mValue) {
			foreach ($mValue as $cKey => $cValue) {
				$min[$cKey] = (isset($min[$cKey])
					? ($min[$cKey] < $cValue ? $min[$cKey] : $cValue)
					: $cValue);
			}
		}

		if ($this->isDebugMode) {
			$this->debug['negative_ideal_solution'] = $min;
		}

		return $min;
	}

	protected function getPositiveDistancePerAlternative()
	{
		$max = $this->getPositiveSolution();
		$positiveDistance = [];

		foreach ($this->data as $mKey => $mValue) {
			foreach ($mValue as $cKey => $cValue) {
				$positiveDistance[$mKey] = (isset($positiveDistance[$mKey])
					? $positiveDistance[$mKey] + pow($cValue - $max[$cKey], 2)
					: pow($cValue - $max[$cKey], 2));
			}

			$positiveDistance[$mKey] = sqrt($positiveDistance[$mKey]);
		}

		if ($this->isDebugMode) {
			$this->debug['positive_distance'] = $positiveDistance;
		}

		return $positiveDistance;
	}

	protected function getNegativeDistancePerAlternative()
	{
		$min = $this->getNegativeSolution();
		$negativeDistance = [];

		foreach ($this->data as $mKey => $mValue) {
			foreach ($mValue as $cKey => $cValue) {
				$negativeDistance[$mKey] = (isset($negativeDistance[$mKey])
					? $negativeDistance[$mKey] + pow($cValue - $min[$cKey], 2)
					: pow($cValue - $min[$cKey], 2));
			}

			$negativeDistance[$mKey] = sqrt($negativeDistance[$mKey]);
		}

		if ($this->isDebugMode) {
			$this->debug['negative_distance'] = $negativeDistance;
		}

		return $negativeDistance;
	}

	public function getPreferenceValuePerAlternative($sorted = false)
	{
		$positive = $this->getPositiveDistancePerAlternative();
		$negative = $this->getNegativeDistancePerAlternative();
		$pref = [];

		foreach ($positive as $key => $value) {
			$pref[$key] = ($negative[$key] / ($negative[$key] + $value));
		}

		if ($sorted) {
			sort($pref);
		}

		if ($this->isDebugMode) {
			$this->debug['preference_value'] = $pref;
		}

		return $pref;
	}

	public function getDebug()
	{
		return $this->debug;
	}
}