<?php

class PadmaFilterGalleryOptions extends PadmaBlockOptionsAPI {

	public $tabs = array(
		'general' => 'General'
	);
	
	public $sets = array(
		
	);

	public $inputs = array(
		'general' => array(
			'gallery' => array(
				'type' => 'select',
				'name' => 'Gallery',
				'label' => 'Select Slider',
				'options' => 'get_galleries()',
				'tooltip' => '',
			),
		)
	);
	
	function get_galleries() {

		$args = array('post_type' => 'padma_filter_gallery', 'posts_per_page' => -1);
		$items = array(
			'-1' => 'Select a gallery'
		);
		
		if( $data = get_posts($args)){
			
			foreach($data as $key){
				$items[$key->ID] = $key->post_title;
			}

		}
		
		return $items;
	}
}