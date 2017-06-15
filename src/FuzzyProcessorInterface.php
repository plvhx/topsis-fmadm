<?php

namespace FMADM\Topsis;

interface FuzzyProcessorInterface
{
	public function withCriterion($name, $criterion);

	public function find($abstract, $name);

	public function getCriterionNames();
}