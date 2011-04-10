<?php
namespace Library\Utility;
/**
 * @class Library.Utility.Pagination
 */
class Pagination {

	private $total_page = 0;
	private $rows_per_page = 10;
	private $page_range = 5;
	private $current_page = 1;
	private $pagination = array(
		'first' => 1,
		'last' => 1,
		'next' => 1,
		'prev' => 1,
		'range' => array()
	);

	public function setCurrentPage($current_page) {
		$this->current_page = $current_page;
	}

	public function getCurrentPage() {
		return $this->current_page;
	}

	public function setRowsPerPage($rows_per_page) {
		$this->rows_per_page = $rows_per_page;
	}

	public function setTotalRows($total_rows) {
		$this->total_page = ($total_rows % $this->rows_per_page > 0 ? (($total_rows - ($total_rows % $this->rows_per_page)) / $this->rows_per_page) + 1 : $total_rows / $this->rows_per_page);
	}

	public function setPageRange($page_range) {
		$this->page_range = $page_range;
	}

	public function getTotalPage() {
		return $this->total_page;
	}

	public function getLastPage() {
		return $this->total_page;
	}

	public function getFirstPage() {
		return 1;
	}

	public function getPrevPage() {
		if ($this->current_page - 1 < 1)
			return 1;

		return $this->current_page - 1;
	}

	public function getNextPage() {
		if ($this->current_page + 1 > $this->total_page)
			return 1;

		return $this->current_page + 1;
	}

	public function getPageRange() {
		if (intval($this->page_range) % 2 == 0)
			$this->page_range += 1;

		$left_right = ($this->page_range - 1) / 2;

		if ($this->current_page <= $left_right) {
			$start = 1;
			$end = $this->page_range + 1;
		} else {
			$start = $this->current_page - $left_right;
			$end = $this->current_page + $left_right + 1;
		}

		if (($this->total_page - $left_right) < $this->current_page) {
			$start = $this->total_page - $this->page_range + 1;
			$end = $this->total_page + 1;
		}

		if ($start < 0) $start = 1;

		$links = array();
		for ($i = $start; $i < $end; $i++) if ($i <= $this->total_page) $links[] = $i;
		return $links;
	}

	public function getStartFrom() {
		return ($this->current_page - 1) * $this->rows_per_page;
	}

	public function getOffset() {
		return $this->rows_per_page;
	}

	public function getPagination() {
		$this->pagination = array(
			'current' => $this->getCurrentPage(),
			'first' => $this->getFirstPage(),
			'last' => $this->getLastPage(),
			'next' => $this->getNextPage(),
			'prev' => $this->getPrevPage(),
			'range' => $this->getPageRange()
		);

		return $this->pagination;

	}

}
