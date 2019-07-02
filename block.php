<?php 

class PadmaFilterGallery extends PadmaBlockAPI {

    public $id 				= 'filter-gallery';    
    public $name 			= 'Filter Gallery';
    public $options_class 	= 'PadmaFilterGalleryOptions';
    public $categories 		= array('content', 'gallery', 'media');
    
    public function init() {

		if(!class_exists('Padma_Filter_Gallery'))
			return false;

	}
			
	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'categories',			
			'name' => 'Categories',
			'description' => 'Categories',
			'selector' => '.categories'
		));

		$this->register_block_element(array(
			'id' => 'categories-ul',			
			'name' => 'Categories UL',
			'description' => 'Categories UL',
			'selector' => '.categories ul'
		));

		$this->register_block_element(array(
			'id' => 'categories-li',			
			'name' => 'Categories Li',
			'description' => 'Categories Li',
			'selector' => '.categories li'
		));

		$this->register_block_element(array(
			'id' => 'filters-div',			
			'name' => 'Gallery content',
			'selector' => '.filtr-container'
		));

		$this->register_block_element(array(
			'id' => 'filters-item',			
			'name' => 'Item',
			'selector' => '.filtr-container .filtr-item'
		));

		$this->register_block_element(array(
			'id' => 'filters-img',			
			'name' => 'Image',
			'selector' => '.filtr-container .filtr-item img'
		));

		$this->register_block_element(array(
			'id' => 'item-position',			
			'name' => 'Item Position',
			'selector' => '.item-position'
		));

		$this->register_block_element(array(
			'id' => 'item-desc',			
			'name' => 'Item description',
			'selector' => '.item-desc'
		));

	}

	function content($block) {

		debug($block);
		
		$gallery = parent::get_setting($block, 'gallery', '');		
		echo do_shortcode('[PFG id="'.$gallery.'"]');
		
	}
	
}