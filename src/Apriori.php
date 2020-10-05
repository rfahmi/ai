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
 *	 	1.1.2. countSupport( items(array) , transactions )
 *	 	*1.1.3. countConfidence( items(array) , transactions )
 *	 1.2. Return frequent
 * *2. createRules()
 *
 * PREDICT
 *  1. $apriori->setSupport(3);
 * 	2. $apriori->setConfidence(75);
 * *3. predict( items )
 *
 */

class Apriori
{
	//CONFIG
	private $support = 2;

	private $confidence = 75;

	private $k1_items = [];

	//SETTER
	public function setRules($arr)
	{
		$this->rules = $arr;
	}

	public function setK1Items($arr)
	{
		$this->k1_items = $arr;
	}

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

		$frequents = $this->createFrequentSet($items, $transactions);
		$rules = $this->createRules($frequents);

		$return['frequents'] = $frequents;

		return $return;
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
			$i != 0 ?: $this->setK1Items($combination);
			$frequent_set[$i] = $combination;
		}

		return $frequent_set;
	}

	private function createRules($items, $transactions)
	{
		$rules = [];
		// Create rules with support and confidence
		foreach ($frequent_set as $key => $value) {
			if ($key > 0) {
				foreach ($value as $combinations) {
					$combination_size = count($combinations['combination']);

					$rule_combination = $this->createCombinations($combinations['combination'], $combination_size, true);
					// echo 'TOP-----------------------------------<br>';
					// dump($rule_combination);
					// echo '<br>BOTTOM-----------------------------------<br>';
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

							// count($rule_array) > 0 ? $rule_array[$i] . ',' . $rule['combination'][$i] : $rule['combination'][$i]
						}
						$rule_body['antecedent'] = $antecedent;
						$rule_body['consequent'] = $consequent;
						$rule_body['support'] = 0;
						$rule_body['confidence'] = 0;

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
