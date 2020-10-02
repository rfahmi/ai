<?php

namespace Rfahmi\Ai;

/**
 * This file is part of the rfahmi\ai package.
 *
 * (c) Fahmi Rizalul - September 2020
 *
 * This project is built for
 * file that was distributed with this source code.
 *
 * ******** STEPS ********
 * INIT
 * 1. init( support , confidence )
 *
 * PROCESS
 * 2. createFrequentSet( items , k )
 * 3. createRules()
 *
 */

class Apriori
{
	//CONFIG
	private $support = 2;

	private $confidence = 75;

	//RESULTS
	private $rules = [];

	//SETTER
	public function setRules($arr)
	{
		$this->rules = $arr;
	}

	//GETTER
	public function getRules()
	{
		return $this->rules;
	}

	public function __construct($support = 2, $confidence = 75)
	{
		$this->support = $support;
		$this->confidence = $confidence;
	}

	public function train($items, $transactions)
	{
		// $itemsets = [];
		$frequents = $this->createFrequentSet($items, $transactions);

		$return['frequents'] = $frequents;

		return $return;
	}

	private function createFrequentSet($items, $transactions)
	{
		$items_length = count($items);
		$itemset = [];
		for ($i = 0; $i < $items_length; $i++) {
			$combination_size = $i + 1;
			$combination = $this->combine($items, $combination_size);
			$temp = [];
			foreach ($combination as $key => $value) {
				$explode = explode(',', $value);
				$child = [
					'combination' => $explode,
					'support' => $this->countSupport($explode, $transactions),
				];
				$combination[$key] = $child;
			}
			$itemset[$i] = $combination;
		}

		return $itemset;
	}

	private function countSupport($comb_items, $trxs)
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

	private function combine($payload, $size)
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

		return $itemset;
	}

	private function createRules($itemsets, $transactions)
	{
		return true;
	}
}
