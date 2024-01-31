<?php

class DrePlanoContas extends TPage
{
	public function __construct()
	{
		parent::__construct();
    }
    
    public function onDerPlanoContas()
    {
        $iframe = new TElement('iframe');
		$iframe->id= "iframe_reports";
		$iframe->src= "https://datastudio.google.com/embed/reporting/d8d99fa7-53af-4baf-87a7-a06c450fb780/page/rzlkB";
		$iframe->frameborder="0";
		$iframe->scrolling="yes";
		$iframe->width="100%";
		$iframe->height="1900px";

		parent::add($iframe);
    }
}
