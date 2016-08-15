<?php

require_once 'posit.php';
	class basket
	{
		public $positions;//массив объектов posit
		public $poscount;//количество
		public $summ;//сумма
		
		public function __construct($src)
		{
			if ($src == 0)
			{
				$n = new posit(0);
				$this->positions = array($n);
				$this->poscount = 0;
				$this->summ = 0;
			}
			else
			{
				$this->positions = $src['positions'];
				$this->poscount = $src['count'];
				$this->summ = $src['sum'];
			}
		}
		
		public function addposit($src)
		{
			if ($this->poscount == 0)
			{
				$this->positions[0] = new posit($src);
			}
			else 
			{
				$this->positions[] = new posit($src);
			}
			$this->poscount++;
			$this->summ = $this->summ + $src['pos']['cost'];
		}
		
		public function undoposit($i)
		{
			$this->poscount--;
			$this->summ = $this->summ - ($this->positions[$i]->position['cost'])*($this->positions[$i]->position['count']);
			unset($this->positions[$i]);
		}
		
		public function changeposit($i,$property,$val)
		{
			if ($property == 'size')
			{
				$cid = $this->positions[$i]->position['pfcid'];
				$sid = $this->positions[$i]->sizes[$val]['id'];
				foreach ($this->positions[$i]->maxcounts as $m)
				{
					if (($m['sid'] == $sid)&&($m['cid'] == $cid))
					{
						if ($m['count'] < $this->positions[$i]->position['count'])
						{
							$this->summ = $this->summ - ($this->positions[$i]->position['cost'])*($this->positions[$i]->position['count'] - $m['count']);
							$this->positions[$i]->position['count'] = $m['count'];
						}
					}
				}
				$this->positions[$i]->changesize($val);
			}
			else if ($property == 'color')
			{
				$cid = $this->positions[$i]->position['sid'];
				$sid = $this->positions[$i]->colors[$val]['id'];
				foreach ($this->positions[$i]->maxcounts as $m)
				{
					if (($m['sid'] == $sid)&&($m['cid'] == $cid))
					{
						if ($m['count'] < $this->positions[$i]->position['count'])
						{
							$this->summ = $this->summ - ($this->positions[$i]->position['cost'])*($this->positions[$i]->position['count'] - $m['count']);
							$this->positions[$i]->position['count'] = $m['count'];
						}
					}
				}
				$this->positions[$i]->changecolor($val);
			}
			else if ($property == 'count')
			{
				$this->summ = $this->summ + ($this->positions[$i]->position['cost'])*($val - $this->positions[$i]->position['count']);
				$this->positions[$i]->changeCount($val);
			}
			else if ($property == 'sc')
			{
				$sid = $this->positions[$i]->sizes[$val['sid']]['id'];
				$sid = $this->positions[$i]->colors[$val['cid']]['id'];
				foreach ($this->positions[$i]->maxcounts as $m)
				{
					if (($m['sid'] == $sid)&&($m['cid'] == $cid))
					{
						if ($m['count'] < $this->positions[$i]->position['count'])
						{
							$this->summ = $this->summ - ($this->positions[$i]->position['cost'])*($this->positions[$i]->position['count'] - $m['count']);
							$this->positions[$i]->position['count'] = $m['count'];
						}
					}
				}
				$this->positions[$i]->changesize($val['sid']);
				$this->positions[$i]->changecolor($val['cid']);
			}
		}
	}
?>