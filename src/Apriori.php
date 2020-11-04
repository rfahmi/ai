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
 * 3. $apriori->setSupport( decimal );
 * 4. $apriori->setConfidence( decimal );
 *
 * TRAIN
 * 1. train( items, transactions )
 *	 1.1. createFrequentSet( items )
 *	 	1.1.1. createCombinations( items , size )
 *	 	1.1.2. countCombination( item )
 *	 1.2. Return frequent_set
 * 2. createRules( frequent_set )
 *	 2.1. countSupport( items[] )
 *	 2.2. countConfidence( items[] , antecedent_lenght )
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

	//HELPER PROPS
	private $items = [];

	private $items_length = 0;

	private $transactions = [];

	private $transactions_length = 0;

	//CONFIG
	private $support = 2;

	private $confidence = 75;

	//SETTER
	public function setSupport($num)
	{
		$this->support = $num;

		return true;
	}

	public function setConfidence($num)
	{
		$this->confidence = $num;

		return true;
	}

	public function setItems($items)
	{
		$this->items = $items;
		$this->items_length = count($items);

		return true;
	}

	public function setTransactions($transactions)
	{
		$this->transactions = $transactions;
		$this->transactions_length = count($transactions);

		return true;
	}

	//GETTER
	public function getFrequentSet()
	{
		$path = __DIR__ . '/../models/apriori_frequentset.json';
		$string = [];
		if (file_exists($path)) {
			$string = file_get_contents($path);
		}

		return json_decode($string, true);
	}

	public function getRules()
	{
		$path = __DIR__ . '/../models/apriori_rules.json';
		$string = [];
		if (file_exists($path)) {
			$string = file_get_contents($path);
		}

		return json_decode($string, true);
	}

	public function __construct()
	{
		//SET LIMIT
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '0');
	}

	//PUBLIC METHOD
	public function train($items, $transactions)
	{
		//INIT
		$this->setItems($items);
		$this->setTransactions($transactions);
		$log['start_time'] = date('Y-m-d H:i:s');

		try {
			$this->frequent_set = $this->createFrequentSet();
			$log['end_frequent'] = date('Y-m-d H:i:s');
			$this->createRules();
			$log['end_rules'] = date('Y-m-d H:i:s');

			file_put_contents(__DIR__ . '\..\log.json', json_encode($log));

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function predict($items)
	{
		$path = __DIR__ . '/../models/apriori_rules.json';
		$rules = [];
		$result = [];

		if (file_exists($path)) {
			$rules = json_decode(file_get_contents($path), true);
			foreach ($rules as $key => $value) {
				if ($this->inArrayAll($items, $value['antecedent'])) {
					$data['item'] = $value['consequent'][0];
					$data['confidence'] = $value['confidence'];
					array_push($result, $data);
				}
			}
			$unique = array_unique($result, SORT_REGULAR);

			return $unique;
		} else {
			return $result;
		}
	}

	//PRIVATE METHODS
	private function createFrequentSet()
	{
		$frequent_set = [];
		for ($i = 0; $i < $this->items_length; $i++) {
			$combination_size = $i + 1;
			$combination = $this->createCombinations($this->items, $combination_size, false);
			$temp = [];
			foreach ($combination as $key => $value) {
				$combination_count = $this->countCombination($value['combination']);
				$combination[$key]['count'] = $combination_count;
			}
			$frequent_set[$i] = $combination;
		}

		for ($i = 0; $i < count($frequent_set); $i++) {
			$frequent_set[$i] = array_filter($frequent_set[$i], function ($x) { return $x['count'] >= $this->support; });
		}
		$frequent_set = array_filter($frequent_set, function ($x) { return count($x) > 0; });

		file_put_contents(__DIR__ . '\..\models\apriori_frequentset.json', json_encode($frequent_set));

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
							if (($j + 1) == $rule_size) {
								array_push($consequent, $rule_combination[$i]['combination'][$j]);
							} else {
								array_push($antecedent, $rule_combination[$i]['combination'][$j]);
							}
						}
						$rule_body['antecedent'] = $antecedent;
						$rule_body['consequent'] = $consequent;

						$result = array_search($rule_combination[$i]['combination'], array_column($this->frequent_set, 'combination', 'count'), true);
						$antecedent_count = $this->countCombination($antecedent);
						$combination_count = $this->countCombination($rule_combination[$i]['combination']);
						$rule_body['support'] = $this->countSupport($combination_count);
						$rule_body['confidence'] = $this->countConfidence($combination_count, $antecedent_count);

						array_push($rules, $rule_body);
					}
				}
			}
		}
		$rules = array_filter($rules, function ($x) { return $x['confidence'] >= $this->confidence; });

		return file_put_contents(__DIR__ . '\..\models\apriori_rules.json', json_encode($rules));
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
						if (strpos($item, (string)$p) === false) {
							$combination = $item !== '' ? $item . ',' . $p : $p;
							$temp[] = $combination;
						}
					} else {
						if ($i3 >= $i2 && strpos($item, (string)$p) === false) {
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

	private function countCombination($comb_items)
	{
		$result = 0;
		foreach ($this->transactions as $key => $value) {
			if ($this->inArrayAll($comb_items, $value)) {
				$result++;
			}
		}

		return $result;
	}

	private function countSupport($combination_count)
	{
		$result = $combination_count / $this->transactions_length;

		return $result;
	}

	private function countConfidence($combination_count, $antecedent_count)
	{
		$result = $antecedent_count > 0 ? $combination_count / $antecedent_count : 0;

		return $result;
	}

	private function inArrayAll($needles, $haystack)
	{
		return empty(array_diff($needles, $haystack));
	}
}
