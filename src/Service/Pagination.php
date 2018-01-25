<?php

namespace Ions\Mvc\Service;

/**
 * Class Pagination
 * @package Ions\Mvc\Service
 */
class Pagination
{
    /**
     * @var int
     */
    public $total = 0;

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var int
     */
    public $limit = 20;

    /**
     * @var int
     */
    public $num_links = 8;

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var string
     */
    public $text_first = '|&lt;';

    /**
     * @var string
     */
    public $text_last = '&gt;|';

    /**
     * @var string
     */
    public $text_next = '&gt;';

    /**
     * @var string
     */
    public $text_prev = '&lt;';

    /**
     * @return string
     */
    public function render()
    {
        $total = $this->total;

        if ($this->page < 1) {
            $page = 1;
        } else {
            $page = $this->page;
        }

        if (!(int)$this->limit) {
            $limit = 10;
        } else {
            $limit = $this->limit;
        }

        $num_links = $this->num_links;
        $num_pages = ceil($total / $limit);

        $this->url = str_replace('%7Bpage%7D', '{page}', $this->url);

        $output = '<ul class="pagination">';

        if ($page > 1) {
            $output .= '<li class="paginate_button first"><a href="' . str_replace(array('&amp;page={page}', '&page={page}'), '', $this->url) . '">' . $this->text_first . '</a></li>';

            if ($page - 1 === 1) {
                $output .= '<li class="paginate_button previous"><a href="' . str_replace(array('&amp;page={page}', '&page={page}'), '', $this->url) . '">' . $this->text_prev . '</a></li>';
            } else {
                $output .= '<li class="paginate_button previous"><a href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $this->text_prev . '</a></li>';
            }
        }

        if ($num_pages > 1) {
            if ($num_pages <= $num_links) {
                $start = 1;
                $end = $num_pages;
            } else {
                $start = $page - floor($num_links / 2);
                $end = $page + floor($num_links / 2);

                if ($start < 1) {
                    $end += abs($start) + 1;
                    $start = 1;
                }

                if ($end > $num_pages) {
                    $start -= ($end - $num_pages);
                    $end = $num_pages;
                }
            }

            for ($i = $start; $i <= $end; $i++) {
                if ($page == $i) {
                    $output .= '<li class="paginate_button active"><span>' . $i . '</span></li>';
                } else {
                    if ($i === 1) {
                        $output .= '<li class="paginate_button"><a href="' . str_replace(array('&amp;page={page}', '&page={page}'), '', $this->url) . '">' . $i . '</a></li>';
                    } else {
                        $output .= '<li class="paginate_button"><a href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a></li>';
                    }
                }
            }
        }

        if ($page < $num_pages) {
            $output .= '<li class="paginate_button next"><a href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $this->text_next . '</a></li>';
            $output .= '<li class="paginate_button last"><a href="' . str_replace('{page}', $num_pages, $this->url) . '">' . $this->text_last . '</a></li>';
        }

        $output .= '</ul>';

        if ($num_pages > 1) {
            return $output;
        }

        return '';
    }

    public function renderResult($text_pagination)
    {
        $page = $this->page;
        $total = $this->total;
        $limit = $this->limit;

        return sprintf(
            $text_pagination,
            $total ? (($page - 1) * $limit) + 1 : 0,
            ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit),
            $total,
            ceil($total / $limit)
        );
    }
}
