<?php
namespace Recipe_Finder;

class Fridge
{
	protected $items = array();

	public function loadFridge($file = "")
	{
		if (empty($file)) {
			throw new \Exception("Filename can't be empty");
			return;
		}
		$data = $this->loadCSVData($file);
		if (count($data) > 0) {
			foreach ($data as $line => $item_line) {
				//unique id for each item to speed up search
				$hash_id = md5($item_line[0]);
				if (isset($this->items[$hash_id])) {
					//item is in the fridge already
					$this->items[$hash_id]->increaseAmount($item_line[1]);
				} else {
					try {
						$item = new Item();
						$item->setName($item_line[0]);
						$item->setAmount($item_line[1]);
						$item->setUnit($item_line[2]);
						$item->setExpiration($item_line[3]);
						$this->items[$hash_id] = $item;
					} catch (Exception $e) {
						echo 'There was an error loading line '.$line.' '.$e->getMessage();
					}
				}
			}
		}
	}

	public function loadCSVData($file = "")
	{
		$file_handle = fopen($file, "r");
		if ($file_handle === false) {
			throw new \Exception("The file couldn't be open");
			return;
		}
		$data = array();
		while ($line = fgetcsv($file_handle)) {
			$data[] = $line;
		}
		fclose($file_handle);

		return $data;
	}

	public function getItems()
	{
		return $this->items;
	}

	public function has($item_name, $amount)
	{
		$hash = md5($item_name);
		$amount = floatval($amount);
		if (!isset($this->items[$hash])) {
			return false;
		}
		$item = $this->items[$hash];
		if ($item->getAmount() < $amount) {
			return false;
		}
		return true;
	}
}