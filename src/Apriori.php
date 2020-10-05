<?php

namespace Rfahmi\Ai;

/**
 * This file is part of the rfahmi\ai package.
 *
 * (c) Fahmi Rizalul - September 2020
 *
 * This package is made for my college research purpose
 * If there is some performance issue in my code, your pull contribution is welcome.
 *
 *
 * ******** HOW THIS PACKAGE WORKS? ********
 * INIT
 * 1. use Rfahmi\Ai\Apriori;
 * 2. $apriori = new Apriori;
 *
 * TRAIN
 * 1. train( items, transactions )
 *	 1.1. createFrequentSet( items , transaction )
 *	 	1.1.1. createCombinations( items , size )
 *	 	1.1.2. countCombination( item , transaction )
 *	 1.2. Return frequent_set
 * 2. createRules( frequent_set )
 *	 *2.1. countSupport( items[] , transactions )
 *	 *2.2. countConfidence( items[] , transactions )
 *
 * PREDICT
 * 1. $apriori->setSupport(3);
 * 2. $apriori->setConfidence(0.75);
 * *3. predict( antecedent[] )
 * *4. Return consequent[]
 *
 */

class Apriori
{
	// RESULT
	private $frequent_set = [];

	private $rules = [];

	//CONFIG
	private $support = 2;

	private $confidence = 75;

	//SETTER
	// public function setRules($arr)
	// {
	// 	$this->rules = $arr;
	// }

	// public function setFrequentSet($arr)
	// {
	// 	$this->frequent_set = $arr;
	// }

	//GETTER
	public function getRules()
	{
		return $this->rules;
	}

	// public function __construct()
	// {
	// }

	public function train($items, $transactions, $support = 2, $confidence = 75)
	{
		$this->support = $support;
		$this->confidence = $confidence;

		try {
			$this->frequent_set = $this->createFrequentSet($items, $transactions);
			$this->rules = $this->createRules();

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function predict($items)
	{
		return true;
	}

	//PRIVATE METHODS
	private function createFrequentSet($items, $transactions)
	{
		$items_length = count($items);
		$transactions_length = count($transactions);

		$frequent_set = [];
		// Create combination & count
		for ($i = 0; $i < $items_length; $i++) {
			$combination_size = $i + 1;
			$combination = $this->createCombinations($items, $combination_size, false);
			$temp = [];
			foreach ($combination as $key => $value) {
				$combination_count = $this->countCombination($value['combination'], $transactions);
				$combination[$key]['count'] = $combination_count;
			}
			$frequent_set[$i] = $combination;
		}

		return $frequent_set;
	}

	private function createRules()
	{
		$rules = [];
		foreach ($this->frequent_set as $key => $value) {
			if ($key > 0) {
				foreach ($value as $combinations) {
					$combination_size = count($combinations['combination']);

					$rule_combination = $this->createCombinations($combinations['combination'], $combination_size, true);
					for ($i = 0; $i < $combination_size; $i++) {
						$rule_size = count($rule_combination[$i]['combination']);

						$rule_body = [];
						$antecedent = [];
						$consequent = [];
						for ($j = 0; $j < $rule_size; $j++) {
							if (($j + 1) === $rule_size) {
								array_push($consequent, $rule_combination[$i]['combination'][$j]);
							} else {
								array_push($antecedent, $rule_combination[$i]['combination'][$j]);
							}
						}
						$rule_body['antecedent'] = $antecedent;
						$rule_body['consequent'] = $consequent;
						$rule_body['support'] = $this->countSupport(count($antecedent), 10);
						$rule_body['confidence'] = $this->countConfidence(count($antecedent), 10);

						array_push($rules, $rule_body);
					}
				}
			}
		}

		return $rules;
	}

	//HELPERS
	private function createCombinations($payload, $size, $repeat = false)
	{
		$itemset = [''];
		for ($i = 0; $i < $size; $i++) {
			$temp = [];
			$i2 = 0;
			foreach ($itemset as $item) {
				$i3 = 0;
				foreach ($payload as $p) {
					if ($repeat) {
						if (strpos($item, $p) === false) {
							$combination = $item !== '' ? $item . ',' . $p : $p;
							$temp[] = $combination;
						}
					} else {
						if ($i3 >= $i2 && strpos($item, $p) === false) {
							$combination = $item !== '' ? $item . ',' . $p : $p;
							$temp[] = $combination;
						}
					}
					$i3++;
				}
				$i2++;
			}
			$itemset = $temp;
		}

		foreach ($itemset as $key => $value) {
			$combination_array = explode(',', $value);
			$child = [
				'combination' => $combination_array,
			];
			$itemset[$key] = $child;
		}

		return $itemset;
	}

	private function countCombination($comb_items, $trxs)
	{
		// dump($comb_items);
		$count = 0;
		foreach ($trxs as $trx) {
			$confirm = 0;
			foreach ($comb_items as $ci) {
				// echo '________Transaction for ' . $ci . '<br>';
				foreach ($trx as $ti) {
					if ($ci === $ti) {
						// echo $ti . ' YES<br>';
						$confirm = 1;
						break;
					} else {
						// echo $ti . ' NO<br>';
						$confirm = 0;
					}
				}
			}
			$confirm == 1 ? $count++ : '';
			// echo '=============================<br>';
			// echo 'Confirm ' . $count . '<br>';
			// echo '=============================<br>';
			// echo '<br>';
		}

		return $count;
	}

	private function countSupport($combination_count, $transactions_length)
	{
		$result = $combination_count / $transactions_length;

		return $result;
	}

	private function countConfidence($combination_count, $antecedent_count)
	{
		$result = $combination_count / $antecedent_count;

		return $result;
	}
}
