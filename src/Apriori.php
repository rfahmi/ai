<?php

namespace Rfahmi\Ai;

/**
 * This file is part of the rfahmi\ai package.
 *
 * (c) Fahmi Rizalul - September 2020
 *
 * This package is made for my college research purpose
 *
 *
 * ******** HOW THIS PACKAGE WORKS? ********
 * INIT
 * 1. use Rfahmi\Ai\Apriori;
 * 2. $apriori = new Apriori;
 * 3. $apriori->setSupport(3);
 * 4. $apriori->setConfidence(75);
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
 * *1. predict( items )
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

		$return['frequents'] = $frequents;

		return $return;
	}

	public function predict($items, $transactions)
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
			$combination = $this->createCombinations($items, $combination_size);
			$temp = [];
			foreach ($combination as $key => $value) {
				$combination_count = $this->countCombination($value['combination'], $transactions);
				$combination[$key]['count'] = $combination_count;
			}
			$i != 0 ?: $this->setK1Items($combination);
			$frequent_set[$i] = $combination;
		}

		$rules = [];
		// Create rules with support and confidence
		foreach ($frequent_set as $key => $value) {
			if ($key > 0) {
				foreach ($value as $combinations) {
					$combination_count = $combinations['count'];
					$antecedent = $combinations['combination'][0];
					$combination_size = count($combinations['combination']);

					$rule_combination = $this->createCombinations($combinations['combination'], $combination_size);
					dump($rule_combination);
					// foreach ($combinations['combination'] as $item_key => $item) {
					// 	// if ($item_key > 0) {
					// 	// 	dump($antecedent);
					// 	// 	echo $item . '=>' . $combination_count;
					// 	// }
					// }
				}
			}
		}
		dd();

		return $frequent_set;
	}

	private function createCombinations($payload, $size)
	{
		$itemset = [''];

		for ($i = 0; $i < $size; $i++) {
			$temp = [];
			$i2 = 0;
			foreach ($itemset as $item) {
				$i3 = 0;
				foreach ($payload as $p) {
					if ($i3 >= $i2 && strpos($item, $p) === false) {
						$combination = $item !== '' ? $item . ',' . $p : $p;
						$temp[] = $combination;
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

	private function countConfidence($combination_count, $transactions_length)
	{
		$antecedent =
		$result = $combination_count / $transactions_length;
	}

	private function createRules($itemsets, $transactions)
	{
		return true;
	}
}
