<?php

require_once 'link.php';
require_once 'category.php';
require_once 'basket.php';
require_once 'posit.php';
class user extends link
{
	public $info;//['name']['surname']['lastname']['addr']['post']['phone']['email']['rdate']
	private $id;
	public $login;
	private $pwd;
	public $log;
	private $sesid;
	private $con;
	public $basket;
	public $library; //['id']['name']['desc']['open']['sub']
	public $cur_cat_id;
	public $cur_cat_path;
	public $cur_cat_desc;
	public $current_category;
	
	public function __construct($ses, $dsn, $usrn, $usrp)
	{
		$this->sesid = $ses;
		$this->con = new link($dsn,$usrn,$usrp);
		$this->con->start($this->id,$this->login,$this->log,$this->info,$this->sesid);
		$this->getlibrary();
		$this->open_basket();
		$this->current_category = new category(0);
		$this->cur_cat_id = 0;
		$this->cur_cat_path = array();
		$this->cur_cat_desc = '';
	}
    public function __sleep()
    {
        return array('info', 'id', 'login', 'pwd', 'log', 'sesid', 'con', 'library', 'cur_cat_id', 'cur_cat_path', 'cur_cat_desc');
    }
    
    public function __wakeup()
    {
        $this->open_basket();
		if ($this->cur_cat_id == 0)
		{
			$this->current_category = new category(0);
		}
		else 
		{
			$this->opencategory($this->cur_cat_id, $this->cur_cat_desc, $this->cur_cat_path);
		}
    }
	
	private function getlibrary()
	{
		$this->con->getlib($this->library, 1);
	}
	
	public function opencategory($id, $desc, $path)
	{
		$this->cur_cat_path = $path;
		$this->cur_cat_id = $id;
		$this->cur_cat_desc = $desc;
		$this->con->getcategory($this->current_category, $id, $desc);
	}
	
	private function log_in()
	{
		$this->con->login($this->id,$this->login,$this->log,$this->info,$this->sesid);
	}
	
	private function log_out()
	{
		$this->con->logout($this->id,$this->login,$this->log,$this->info,$this->sesid);
	}
	
	private function register()
	{
		echo 'в разработке <br />';
		/*$this->con->regist($this->id,$this->login,$this->log,$this->info,$this->sesid);*/
	}
	
	private function open_basket()
	{
		$this->con->getbasket($this->id,$this->basket);
	}
	
	public function add_to_basket($id, $colorid, $sizeid, $catpath, $count)
	{
		$once = true;
		//print_r($this->basket->positions);
		//echo 'new: '.$id.' '.$colorid.' '.$sizeid.' '.$count;
		foreach ($this->basket->positions as &$p)
		{
			if (($p->position['pid'] == $id)&&($p->position['color'] == $colorid)&&($p->position['sid'] == $sizeid))
			{
				/*echo 'pid - right';
				if ($p->position['color'] == $colorid)
				{
					echo 'color - right';
					if ($p->position['sid'] == $sizeid)
					{
						echo 'size - right';*/
				$this->con->repeatadd($p->position['id']);
				$p->position['count']++;
				$this->basket->summ += $p->position['cost'];
				$once = false;
				break;
					/*}
				}*/
			}
		}
		if ($once)
		{
			for($i = 0; $i < count($this->current_category->products); $i++)
			{
				if ($this->current_category->products[$i]->info['id'] == $id)
					break;
			}
			for($j = 0; $j < count($this->current_category->products[$i]->colors); $j++)
			{
				if ($this->current_category->products[$i]->colors[$j]['name'] == $colorid)
					break;
			}
			for($k = 0; $k < count($this->current_category->products[$i]->sizes); $k++)
			{
				if ($this->current_category->products[$i]->sizes[$k]['id'] == $sizeid)
					break;
			}
			$this->current_category->products[$i]->getstate($j, $k, $count);
			$this->con->addtobasket($this->id,$this->basket,$this->current_category->products[$i]->choise,$catpath);
		}
	}
	
	public function undo_from_basket($id)
	{
		for ($i=0; $i<count($this->basket->positions); $i++)
		{
			if ($this->basket->positions[$i]->position['id'] == $id)
				break;
		}
		$this->con->undofrombasket($id);
		$this->basket->undoposit($i);
	}
	
	public function change_basket_posit($id,$property,$val)// size - id || color - id || count - count || sc - ['sid']['cid']
	{
		for ($i=0; $i<count($this->basket->positions); $i++)
		{
			if ($this->basket->positions[$i]->position['id'] == $id)
				break;
		}
		switch ($property)
		{
			case 'size':
			{
				for ($j=0; $j<count($this->basket->positions[$i]->sizes); $j++)
				{
					if ($this->basket->positions[$i]->sizes[$j]['id'] == $val)
					{
						$val = $this->basket->positions[$i]->sizes[$j];
						break;
					}
				}
				$this->con->changeS($id, 
									$val,
									$this->basket->positions[$i]->position['count'], 
									$this->basket->positions[$i]->position['pfcid']);
				$this->basket->changeposit($i,$property,$j);
				break;
			}
			case 'color':
			{
				echo ' color!! ';
				for ($j=0; $j<count($this->basket->positions[$i]->colors); $j++)
				{
					if ($this->basket->positions[$i]->colors[$j]['id'] == $val)
					{
						$val = $this->basket->positions[$i]->colors[$j];
						print_r($val);
						break;
					}
				}
				$this->con->changeColor($id,			
										$val,
										$this->basket->positions[$i]->position['count'],
										$this->basket->positions[$i]->position['sid']);
				$this->basket->changeposit($i,$property,$j);
				break;
			}
			case 'count':
			{
				$this->con->changeCount($id, $val);
				$this->basket->changeposit($i,$property,$val);
				break;
			}
			case 'sc':
			{
				echo ' !! sc !! </ br>';
				$sc = array(
					'sid' => 0,
					'cid' => 0);
				for ($j=0; $j<count($this->basket->positions[$i]->colors); $j++)
				{
					if ($this->basket->positions[$i]->colors[$j]['id'] == $val['cid'])
					{
						$color = $this->basket->positions[$i]->colors[$j];
						$sc['cid'] = $j;
						print_r($color);
						break;
					}
				}
				for ($j=0; $j<count($this->basket->positions[$i]->sizes); $j++)
				{
					if ($this->basket->positions[$i]->sizes[$j]['id'] == $val['sid'])
					{
						$size = $this->basket->positions[$i]->sizes[$j];
						$sc['sid'] = $j;
						print_r($size);
						break;
					}
				}
				$this->con->changeSC($id, $color, $size, $this->basket->positions[$i]->position['count']);
				$this->basket->changeposit($i,$property,$sc);
				break;
			}
		}
	}
	
	private function pay()
	{
		echo 'в разработке <br />';
	}
}
?>