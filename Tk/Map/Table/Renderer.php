<?php
namespace Tk\Map\Table;
use Tk\Exception;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
class Renderer extends \Tk\Table\Renderer\Dom\Table
{


    /**
     * @param \Tk\Table $table
     * @return static
     */
    static function create($table = null)
    {
        $obj = new static($table);
        return $obj;
    }

    /**
     *
     */
    public function init()
    {
        $request = \Tk\Config::getInstance()->getRequest();
        if ($request->has('list')) {
            $this->doGetList($request);
        }

    }

    /**
     * @param $request
     */
    protected function doGetList($request)
    {
        try {
            $data = array('markerList' => array());

            /** @var \Tk\Map\Db\Location $obj */
            foreach ($this->getTable()->getList() as $obj) {
                $m = $obj->getMarker();
                if ($m->icon instanceof \Tk\Uri)
                    $m->icon = $m->icon->__toString();
                $m->html = $this->getObjectHtml($obj);
                //vd(json_encode($m, \JSON_PRETTY_PRINT));
                $data['markerList'][] = $m;
            }
            \Tk\ResponseJson::createJson($data)->send();
        } catch (Exception $e) {
            \Tk\ResponseJson::createJson(array('error' => $e->getMessage()), \Tk\ResponseJson::HTTP_INTERNAL_SERVER_ERROR)->send();
        }
        exit();
    }

    /**
     * @param $obj
     * @return string
     */
    public function getObjectHtml($obj)
    {
        $htmlRow = '';

        /* @var \Tk\Table\Cell\Iface $cell */
        foreach($this->getTable()->getCellList() as $k => $cell) {
            if (!$cell->isVisible()) continue;

            $html = $cell->getCellHtml($obj, $this->rowId);
            if (is_callable($cell->getOnCellHtml())) {
                $h = call_user_func_array($cell->getOnCellHtml(), array($cell, $obj, $html));
                if ($h !== null) $html = $h;
            }
            if ($html) {

                $cell->addCss('td');
                $cell->addCss('m' . ucfirst($cell->getProperty()));
                $htmlRow .= sprintf('<div class="%s">%s</div>', $cell->getCssString(), $html);
            }
        }

        return '<div class="row-group">' . $htmlRow . '</div>';
    }


    /**
     * Execute the renderer.
     * The returned object can be anything that you need to render
     * the output.
     *
     * @return mixed
     * @throws \Dom\Exception
     * @throws \Tk\Db\Exception
     */
    public function show()
    {
        $this->init();

        //$this->enableFooter(false);
        $this->getTable()->addCss('map-table');

        $template = parent::show();

        \App\Ui\Js::includeGoogleMaps($template);
        $template->appendJsUrl(\Tk\Uri::create('/vendor/ttek/tk-map/js/jquery.tkMap.js'));

        $template->setAttr('table', 'data-ajax-url', \Tk\Uri::create()->set('list'));

        $js = <<<JS
jQuery(function($) {
  
  $('.tk-table .table').each(function (i) {
    if (!$.fn.tkMap) return;
    $(this).tkMap({});
  });
  
});
JS;
        $template->appendJs($js);


        $css = <<<CSS
.tk-table-wrap {
    height: 650px;
    border: 1px solid #EFEFEF;
    box-shadow: 1px 1px 2px #CCC;
    overflow: hidden;
}
.tk-marker-html {
  max-width: 300px;
}
CSS;
        $template->appendCss($css);

        return $template;
    }


}