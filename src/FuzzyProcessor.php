<?php

namespace FMADM\Topsis;

class FuzzyProcessor implements FuzzyProcessorInterface
{
	/**
	 * @var array
	 */
	private $fuzzy = [];

	public function __construct($name = null, $criterion = null)
	{
		if (is_string($name) && is_array($criterion)) {
			$this->fuzzy[$name] = ['criterion' => $criterion, 'normalized' => false];
		}

		$this->remapExistingCriterion();
	}

	public function withCriterion($name = null, $criterion = null)
	{
		if (!is_string($name) || !is_array($criterion)) {
			throw new \Exception("Name must be a string and criterion must be a non-empty array.");
		}

		$q = clone $this;
		$q->fuzzy[$name] = ['criterion' => $criterion, 'normalized' => false];

		$q->remapExistingCriterion();

		return $q;
	}

	protected function remapExistingCriterion()
	{
		foreach ($this->fuzzy as $name => $val) {
			if (!isset($val['criterion']['lower-bound']) && !isset($val['criterion']['upper-bound']) &&
				!$val['normalized']) {
				$keys = $val['criterion'];
				$values = array_map(function($q) use ($keys) {
					return ($q / sizeof($keys));
				}, array_map(function($x) { return ($x + 1); }, array_keys($keys)));

				$this->fuzzy[$name]['criterion'] = array_combine($keys, $values);
				$this->fuzzy[$name]['normalized'] = true;
			}
		}
	}

	public function find($abstract, $name)
	{
		if (!isset($this->fuzzy[$name])) {
			throw new \Exception(
				sprintf("Criterion name %s not defined.", $name)
			);
		}

		$current = $this->fuzzy[$name]['criterion'];

		if (isset($current['lower-bound']) && isset($current['upper-bound'])) {
			if (!is_int($abstract) && !is_float($abstract)) {
				throw new \InvalidArgumentException(
					sprintf("Parameter %s must be an integer or double in bounded fuzzy mode.", $abstract)
				);
			}

			$normalized = $abstract / $current['upper-bound'];

			$abstract = ($abstract <= $current['lower-bound']
				? 0
				: (($abstract >= $current['upper-bound'])
					? 1
					: $normalized));
		}
		else {
			$abstract = $current[$abstract];
		}

		return $abstract;
	}

	public function getCriterionNames()
	{
		return array_keys($this->fuzzy);
	}
}