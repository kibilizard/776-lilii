<?php
	class product
	{
		public $info;//['id']['name']['foto']['cost']['desc']['compos'](['care']->['foto']['type']['desc'])
						//(['compable']->['id']['path']['foto']['color']['cname']['name']['desc']['cost'](['sizes']->['id']['val']['desc']))
		public $choise;//['id']['pfcid']['color']['fid']['size']['count']
		public $sizes;//->['id']['size']['desc']
		public $colors;//->['id']['val']['name']['main']['mcol']['fotos':->['id']['pfcid']['path']]
		public $counts;//->['sid']['cid']['count']
		
		public function __construct($src)
		{
			if ($src == 0)
			{
				$this->info = array();
				$this->choise = array();
				$this->sizes = array();
				$this->colors = array();
				$this->counts = array();
			}
			else
			{
				$this->info = $src['info'];
				$this->sizes = $src['sizes'];
				$this->colors = $src['colors'];
				$this->counts = $src['counts'];
				foreach ($this->colors as $col)
				{
					if ($col['mcol'])
					{
						$this->choise = array(
						'id' => $this->info['id'],
						'pfcid' => $col['fotos'][0]['pfcid'],
						'color' => $col['val'],
						'fid' => $col['fotos'][0]['id'],
						'size' => 0,
						'count' => 0);
					}
				}
			}
		}
		
		public function getstate ($colorid, $sizeid, $count)
		{
			$this->choise = array(
			'id' => $this->info['id'],
			'pfcid' => $this->colors[$colorid]['fotos'][0]['pfcid'],
			'color' => $this->colors[$colorid]['val'],
			'fid' => $this->colors[$colorid]['fotos'][0]['id'],
			'size' => $this->sizes[$sizeid]['id'],
			'count' => $count);
		}
	}
	class category
	{
		public $id;
		public $description;
		public $products;
		public $prodcount;
		
		public function __construct($src)
		{
			if ($src == 0)
			{
				$this->id = 0;
				$this->description = '';
				$this->products = new product(0);
				$this->prodcount = 0;
			}
			else
			{
				$this->id = $src['id'];
				$this->description = $src['desc'];
				$this->products = $src['prod'];
				$this->prodcount = $src['count'];
			}
		}
		
		private function addproducttobasket($id, $colorid, $sizeid, $count)
		{
			$this->products[$id]->getstate($colorid, $sizeid, $count);
		}
	}
?>