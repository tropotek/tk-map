<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Map\Table;

use Form\Renderer as FormRenderer;
use Table\Table;



/**
 * The dynamic form renderer
 *
 *
 * @package Map\Table
 */
class Renderer extends \Mod\Module
{

    /**
     * @var Table
     */
    protected $table = null;


    /**
     * @var StaticRenderer
     */
    protected $formRenderer = null;


    /**
     * @var array
     */
//    protected $queryCallback = null;


    /**
     * Create the object instance
     *
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->setTable($table);
        $this->setInstanceId($table->getInstanceId());
        $this->formRenderer = new FormRenderer($table->getForm());
        $this->formRenderer->showEnabled(false);
        $this->formRenderer->setInstanceId($this->getInstanceId());

        $this->addEvent('list', 'getMarkers');

    }

    /**
     * Create a new form with a new form renderer
     *
     * @param Table $table
     * @return Renderer
     */
    static function create($table)
    {
        $obj = new self($table);
        return $obj;
    }

    /**
     * init
     *
     */
    public function init()
    {
        $this->table->init();
    }

    public function getMarkers()
    {
        if (!$this->getRequest()->checkReferer()) {
            throw new \Tk\Exception('Process Error');
        }
        $markers = array();
        foreach($this->table->getList() as $item) {
            $obj = new \stdClass();
            $obj->id = $item->id;
            $obj->location = array($item->getMapLat(), $item->getMapLng());
            $obj->zoom = $item->getMapZoom();
            $this->insertRow2($item, $obj);
            $markers[] = $obj;
        }
        \Tk\Response::createJsonResponse($markers)->flush();
        exit;
    }

    /**
     * Insert an object's cells into a row
     *
     * @param \Tk\ObjectUtil $obj
     * @param stdClass $jObj
     * @return void
     */
    protected function insertRow2($obj, $jObj)
    {
        /* @var $cell \Table\Cell\Interface */
        foreach ($this->table->getCellList() as $i => $cell) {
            $label = $cell->getLabel();
            $data = $cell->getTd($obj);

            $jObj->$label = $data;
            if ($cell->isKey()) {
                $jObj->key = $cell->getProperty();
            }
            $jObj->cssClass = $cell->getRowClassList();
        }
    }


    /**
     * execute
     *
     */
    public function doDefault()
    {
        $this->table->execute();
    }


    /**
     * Show
     *
     */
    public function show()
    {
        $template = $this->getTemplate();
        $list = $this->table->getList();

        // Table Events
        if (count($this->table->getActionList()) > 0 || count($this->table->getFilterList()) > 0) {
            $template->setChoice('TableEvents');
        }
        //$this->showActions($this->table->getActionList());
        $this->showFilters($this->table->getFilterList());


        //$this->showTh($this->table->getCellList());

//        if ($list && count($list)) {
//            $tool = $list->getTool();
//            if ($tool) {
//                $results = \Table\Ui\Results::createFromTool($tool);
//                $results->show();
//                $template->replaceTemplate('Results', $results->getTemplate());
//
//                $pager = \Table\Ui\Pager::createFromTool($tool);
//                $pager->show();
//                $template->replaceTemplate('Pager', $pager->getTemplate());
//
//                $limit = \Table\Ui\Limit::createFromTool($tool);
//                $limit->show();
//                $template->replaceTemplate('Limit', $limit->getTemplate());
//            }
//            $this->showTd($list);
//        }




        $css = <<<CSS
.mapTableRenderer {
  position: relative;
  border-top: 1px solid #CCC;
}
.mapTableRenderer img {
  max-width: none;
}
.mapTableRenderer .map {
  min-height:800px;
}
.resultsPanel {
display: none;
  background-color: #FFF;
  position: absolute;
  right: 0px;
  top: 0px;
  width: 30%;
  height: 100%;
  overflow-y: scroll;
}
.resTable {
  padding: 10px;
}
.resTr {
  display: inline-block;
  width: 100%;
  margin: 0px 0px 10px 0px;
  padding: 10px 0px 10px 0px;
  border-bottom: 1px solid #CCC;
}
.resTd {
  float: left;
  margin: 0px 10px 0px 10px;
}


CSS;
        $template->appendCss($css);


        $template->appendJsUrl(\Tk\Url::create('http://maps.googleapis.com/maps/api/js?sensor=false'));
        /* TODO: move to assets public folder or find another solution */
        $template->appendJsUrl(\Tk\Url::createLibUrl('/Map/Google/js/markerclusterer/src/markerclusterer.js'));



        $widgetId = $this->table->getId().'_mapRenderer';
        $widgetInit = $this->table->getId().'_mapInit';
        $template->setAttr('map', 'id', $widgetId);

        $formId = $this->table->getForm()->getId();
        $uri = $this->getUri()->set('list')->toString();


        $js = <<<JS
/**
 * The MarkerManager object.
 * @type {MarkerManager}
 */

function $widgetInit() {
    var myLatlng = new google.maps.LatLng(-37.890710072346934, 144.70056814575196);
    var myOptions = {
        zoom: 13,
        minZoom: 4,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    google.maps.InfoWindow.prototype.isOpen = false;

    var markers = [];
    var map = new google.maps.Map(document.getElementById('$widgetId'), myOptions);
//    MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_PATH_ = 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/' +
//    'images/m';
    var mc = new MarkerClusterer(map, null, {'zoomOnClick': false, 'maxZoom': 15 });

//    google.maps.event.addListener(map, 'zoom_changed', function() {
//        vd(map.getZoom());
//    });
//    google.maps.event.addListener(map, 'dragend', function() {
//        vd(map.getCenter().toString());
//    });

    function createMarkers(map) {
        $.post('$uri', $('#{$formId}').serialize(), function (data) {
            for(var i = 0; i < data.length; i++) {
                addMarker(data[i]);
            }
            mc.addMarkers(markers);
        });
    }

    function addMarker(obj) {
      var html = '<div class="bubbleBox"><div class="bubbleWrap"><div class="title">Gisborne Veterinary Clinic</div> <div class="gm-rev"><a target="_blank" href="#">more info ÃÂÃÂ»</a></div>'+
        '<div class="basicinfo"><div class="address">12 Robertson Road, Gisborne VIC, Australia</div><div class="phone">(03) 5428 2805</div></div></div></div>';
//      var html = '<div><h5><a href="#" title="">'+obj.id+'</a></h5>' + '<p>-</p></div>';

//
//      var html = '<div class="bubbleBox"><div class="bubbleWrap">';
//      $(obj).each(function (e) {
//          if (
//      });
//      html = html + '</div></div>';

      var infowindow = new google.maps.InfoWindow({
        content: html
      });
      var marker = new google.maps.Marker({
          position: new google.maps.LatLng(obj.location[0], obj.location[1]),
          map: map,
          title: obj.name
      });
      google.maps.event.addListener(marker, 'click', function() {
        if (infowindow.isOpen) {
          infowindow.close();
          infowindow.isOpen = false;
        } else {
          infowindow.open(map, marker);
          infowindow.isOpen = true;
        }
      });
      markers[markers.length] = marker;
    }
    createMarkers(map);


}
google.maps.event.addDomListener(window, 'load', $widgetInit);


JS;
        $template->appendJs($js);



    }





    /**
     * Get the item object list array
     *
     * @return ArrayIterator
     */
    public function getList()
    {
        return $this->table->getList();
    }

    /**
     * Set the id to be the same as the table. This will be used by the
     * cells for the event key
     *
     * @param Table $table
     * @return Renderer
     */
    public function setTable(Table $table)
    {
        $this->setInstanceId($table->getInstanceId());
        $this->table = $table;
        return $this;
    }

    /**
     * Get the table object
     *
     * @return Table
     */
    public function getTable()
    {
    	return $this->table;
    }


    /**
     * makeTemplate
     *
     * @return string
     */
    public function __makeTemplate()
    {
        $formId = $this->table->getForm()->getId();
        $tableId = $this->table->getTableId();
        $action = htmlentities($this->getUri()->toString());

        $xmlStr = <<<XML
<?xml version="1.0"?>
<div class="tk-table" id="$tableId">
  <form id="$formId" method="post" action="$action" class="form-inline">


    <!-- Table Header -->
    <div class="tableEvents" choice="TableEvents">
      <div class="filters" choice="filters">
        <div class="fieldBox" var="fields"></div>
        <div class="events" var="events"></div>
      </div>
      <div class="actions" choice="actions" var="action"></div>
    </div>

    <div class="mapTableRenderer" var="wrapper">
      <div class="map" var="map"></div>
      <div class="resultsPanel" var="panel">



        <div class="ctrlBox">
          <div class="ctrl tk-results">
            <div var="Results"></div>
          </div>
          <div class="ctrl tk-limit">
            <div var="Limit"></div>
          </div>
          <div class="ctrl tk-pager">
            <div var="Pager"></div>
          </div>
        </div>

        <!-- Table -->
        <div class="resTable" var="tableData">
          <div class="resTr" var="tr" repeat="tr">
              <div class="resTd" var="td" repeat="td">&#160;</div>
          </div>
        </div>

        <!-- Table Controls -->
        <div class="ctrlBox">
          <div class="ctrl tk-pager">
            <div var="Pager"></div>
          </div>
          <div class="ctrl tk-results">
            <div var="Results"></div>
          </div>
          <div class="ctrl tk-limit">
            <div var="Limit"></div>
          </div>
        </div>



      </div>
    </div>
  </form>
</div>
XML;

        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


    /**
     * Render the filter fields
     *
     * @param array $filterList
     */
    public function showFilters($filterList)
    {
        if (!count($filterList)) {
            return;
        }
        $template = $this->getTemplate();
        $this->formRenderer->showEvents($template);
        $this->formRenderer->showFields($template);
        $template->setChoice('filters');
    }

    /**
     * Render the action icons
     *
     * @param array $actionList
     */
    public function showActions($actionList)
    {
        if (!count($actionList)) {
            return;
        }
        $template = $this->getTemplate();
        $template->setChoice('actions');
        /* @var $action \Table\Action\Iface */
        foreach ($actionList as $action) {
            $data = $action->getHtml($this->table->getList());
            if ($data instanceof \Dom\Template) {
                $template->appendTemplate('action', $data);
            } else {
                $template->appendHTML('action', $data);
            }
        }
    }

    /**
     * Render the table headers
     *
     * @param array $cellList
     */
    public function showTh($cellList)
    {
        $template = $this->getTemplate();
        /* @var $cell \Table\Cell\Iface */
        foreach ($cellList as $cell) {
            $repeat = $template->getRepeat('th');

            if ($cell->getOrderProperty()) {
                if ($cell->getOrder() == \Table\Cell\Iface::ORDER_ASC) {
                    $repeat->addClass('th', 'orderAsc');
                } else if ($cell->getOrder() == \Table\Cell\Iface::ORDER_DESC) {
                    $repeat->addClass('th', 'orderDesc');
                }
            }
            if ($cell->isKey()) {
                $repeat->addClass('th', 'key');
            }

            $data = $cell->getTh();
            if ($data === null) {
                $data = '&#160;';
            }
            if ($data instanceof \Dom\Template) {
                $repeat->insertTemplate('th', $data);
            } else {
                $repeat->insertHTML('th', $data);
            }
            $repeat->appendRepeat();
        }
    }

    /**
     * Render the table data rows
     *
     * @param array $list
     * @throws \Dom\Exception
     */
    public function showTd($list)
    {
        $template = $this->getTemplate();
        $idx = 0;
        /* @var $obj \Tk\ObjectUtil */
        foreach ($list as $obj) {
            $repeatRow = $template->getRepeat('tr');
            $rowClassArr = $this->insertRow($obj, $repeatRow);

            $rowClass = 'r_' . $idx . ' ' . $repeatRow->getAttr('tr', 'class') . ' ';
            if (count($rowClassArr) > 0) {
                $rowClass .= implode(' ', $rowClassArr);
            }
            $rowClass = trim($rowClass);
            $repeatRow->addClass('tr', $rowClass);
            $repeatRow->appendRepeat();
            $idx++;
        }
    }

    /**
     * Insert an object's cells into a row
     *
     * @param \Tk\ObjectUtil $obj
     * @param \Dom\Template $template The row repeat template
     * @return array
     * @throws \Dom\Exception
     */
    protected function insertRow($obj, $template)
    {
        $rowClassArr = array();
        /* @var $cell \Table\Cell\Interface */
        foreach ($this->table->getCellList() as $i => $cell) {
            if ($i == 0) {
                $cell->clearRowClass();
            }
            //$cell->clearCellClass();
            $repeat = $template->getRepeat('td');
            $data = $cell->getTd($obj);
            if ($data === null) {
                $data = '&#160;';
            }

            $repeat->addClass('td', 'm' . ucfirst($cell->getProperty()));
            if (count($cell->getCellClassList())) {
                $repeat->addClass('td', implode(' ', $cell->getCellClassList()));
            }
            if ($cell->isKey()) {
                $repeat->addClass('td', 'key');
            }

            $repeat->setAttr('td', 'title', $cell->getLabel());
            $rowClassArr = array_merge($rowClassArr, $cell->getRowClassList());

            if ($data instanceof \Dom\Template) {
                $repeat->insertTemplate('td', $data);
            } else {
                $repeat->insertHTML('td', $data);
            }
            $repeat->appendRepeat();
        }
        return $rowClassArr;
    }
}