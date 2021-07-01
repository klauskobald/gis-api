<?php
/**
 * User: klausk
 * Date: 29.06.21
 * Time: 17:44
 */


class texture_map {

	protected $_w, $_h;
	protected $_im, $_col;
	protected $xScale, $yScale, $x0, $y0;

	public function __construct($minX, $minY, $maxX, $maxY, $outSize) {
		$this->x0 = $minX;
		$this->y0 = $minY;
		$this->xScale = ($maxX - $minX);
		$this->yScale = ($maxY - $minY);

		$r = $this->yScale / $this->xScale;
		$deform = cos(deg2rad(($maxY + $minY) / 2));
		$aspect = $r / $deform;

		$this->_w = intval($outSize);
		$this->_h = intval($outSize * $aspect);
		echo " > scale=$this->xScale x $this->yScale deform=$deform w x h = $this->_w x $this->_h \n";
		$this->_im = imagecreatetruecolor($this->_w, $this->_h);
		imagecolorallocate($this->_im, 0, 0, 0);
		$this->_col = imagecolorallocate($this->_im, 255, 255, 255);
	}

	public function addPoints($pts) {
		$a = array();
//		echo " > draw shape: ".count($pts)." pts\n";
		foreach ($pts as $coord) {
			$x = ($coord[0] - $this->x0) / $this->xScale * $this->_w;
			$y = $this->_h - (($coord[1] - $this->y0) / $this->yScale * $this->_h);
			$a[] = round($x);
			$a[] = round($y);
		}
#		echo ".";
		imagefilledpolygon($this->_im, $a, count($a) / 2, $this->_col);

	}


	protected function _create() {
		imagetruecolortopalette($this->_im, true, 2);
	}

	public function stream() {
		$this->_create();
		ob_end_clean();
		header('Content-type: image/png');
		echo imagepng($this->_im);
		exit(0);
	}

}