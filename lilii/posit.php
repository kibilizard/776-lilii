<?php
	class posit
	{
		public $position;//['id']['pid']['desc']['cost']['pfcid']['colid']['color']['foto']['sid']['size']['sdesc']['count']['cpath']
		public $sizes;//['id']['size']['sdesc']
		public $colors;//['id']['color']['name']['fid']['foto']
		public $maxcounts;//['sid']['cid']['count']
		
		public function __construct($src)
		{
			if ($src == 0)
			{
				$this->position = array();
				$this->sizes = array();
				$this->colors = array();
			}
			else
			{
				$this->position = $src['pos'];
				$this->sizes = $src['sizes'];
				$this->colors = $src['colors'];
				$this->maxcounts = $src['maxcounts'];
			}
		}
	
		public function changesize($i)
		{
			$this->position['id'] = $this->sizes[$i]['id'];
			$this->position['size'] = $this->sizes[$i]['size'];
			$this->position['sdesc'] = $this->sizes[$i]['sdesc'];
		}
		
		public function changecolor($i)
		{
			$this->position['pfcid'] = $this->colors[$i]['id'];
			$this->position['color'] = $this->colors[$i]['name'];
			$this->position['foto'] = $this->colors[$i]['foto'];
		}
		
		public function changecount($val)
		{
			$this->position['count'] = $val;
		}
	}
?>