<?php

class HtmlSidebar {

    protected $leftItems;      ///< An array with all items that should be displayed at the left part of the sidebar
    protected $rightItems;     ///< An array with all items that should be displayed at the right part of the sidebar
    protected $htmlPage;       ///< A HtmlPage object that will be used to add javascript code or files to the html output page.
    protected $htmlForm;       ///< Parameter that includes the html of the form that should be shown within the sidebar
    protected $name;           ///< Name of the sidebar that will be shown when sidebar changed to vertical mode on small devices
    protected $type;           ///< Navbar type. There is the @b default and the @b filter type possible.
    protected $id;             ///< The id of the sidebar.
    protected $customCssClass; ///< A css class name that should be added to the main nav tag of the sidebar

    /**
     * creates the object of the module menu and initialize all class parameters
     * @param string    $id       Html id of the sidebar
     * @param string    $name     Name of the sidebar that will be shown when sidebar changed to vertical mode on small devices
     * @param \HtmlPage $htmlPage Optional a HtmlPage object that will be used to add javascript code
     *                            or files to the html output page.
     * @param string    $type     Different types of the sidebar can be defined.
     *                            default: will be the standard sidebar of all modules.
     *                            filter:  should be used if this sidebar is used to filter data of within the script.
     */

    public function __construct($id, $name = null, HtmlPage $htmlPage = null, $type = 'default') {
        global $gL10n;

        if ($name === null) {
            if ($type === 'default') {
                $name = $gL10n->get('SYS_MENU');
            } elseif ($type === 'filter') {
                $name = $gL10n->get('SYS_FILTER');
            }
        }

        if ($htmlPage instanceof \HtmlPage) {
            $this->htmlPage = & $htmlPage;
        }

        $this->leftItems = array();
        $this->rightItems = array();
        $this->htmlForm = '';
        $this->name = $name;
        $this->type = $type;
        $this->id = $id;
        $this->customCssClass = '';
    }

    public function setLeftItems(array $leftItems) {
        $this->leftItems = $leftItems;
    }

    public function setRightItems(array $rightItems) {
        $this->rightItems = $rightItems;
    }

    /**
     * Creates the html for the menu entry.
     * @param string[] $data An array with all data if the item. This will be @id, @url, @text and @icon.
     * @return string Returns the html for the menu entry
     */
    protected function createHtmlLink(array $data, $hasChilds) {
        $icon = '';
        $dataSidebarAttr = '';
        $html = '';

        if ($data['icon'] !== '') {
            $icon = '<img src="' . $data['icon'] . '" alt="' . strip_tags($data['text']) . '" />';
        }
        
        if ($hasChilds)
        {
            $dataSidebarAttr = ' data-sidebar="' . $data['id'] . '"';
        }

        $html = '
            <a href="' . $data['url'] . '" class="' . $data['class'] . '" id="' . $data['id'] . '" ' . $dataSidebarAttr . '>
                <b>' . $icon . $data['text'] . '</b>
                <span class="glyphicon glyphicon_behind glyphicon-menu-right"></span>
            </a>';

        return $html;
    }

    /**
     * This method adds an additional css class to the main nav tag of the menu.
     * @param string $className The name of a css class that should be add to the main nav tag of the manu
     */
    public function addCssClass($className) {
        $this->customCssClass = ' ' . $className;
    }

    /**
     * Add a form to the menu. The form will be added between the left and the right part of the sidebar.
     * @param string $htmlForm A html code of a form that will be added to the menu
     */
    public function addForm($htmlForm) {
        $this->htmlForm = $htmlForm;
    }

    /**
     * Add a new item to the menu. This can be added to the left or right part of the sidebar.
     * You can also add another item to an existing dropdown item. Therefore use the @b $parentItem parameter.
     * @param string $id          Html id of the item.
     * @param string $url         The url of the generated link of this item.
     * @param string $text        The text of the item and the generated link.
     * @param string $icon        Icon of the menu item, that will also be linked
     * @param string $orientation The item can be shown at the @b left or @b right part of the sidebar.
     * @param string $parentItem  All items should be added to the @b sidebar as parent. But if you
     *                            have already added a dropdown than you can add the item to that
     *                            dropdown. Just commit the id of that item.
     * @param string $class       Optional a css class that will be set for the item.
     */
    public function addItem($id, $url, $text, $icon, $orientation = 'left', $parentItem = 'sidebar', $class = '') {
        $urlStartRegex = '/^(http(s?):)?\/\//';

        // add root path to link unless the full URL is given
        if ($url !== '' && $url !== '#' && preg_match($urlStartRegex, $url) === 0) {
            $url = ADMIDIO_URL . $url;
        }

        // add THEME_URL to images unless the full URL is given
        if ($icon !== '' && preg_match($urlStartRegex, $icon) === 0) {
            $icon = THEME_URL . '/icons/' . $icon;
        }

        $item = array('id' => $id, 'text' => $text, 'icon' => $icon, 'url' => $url, 'class' => $class);

        if ($orientation === 'left') {
            if ($parentItem === 'sidebar') {
                $this->leftItems[$id] = $item;
            } elseif (array_key_exists($parentItem, $this->leftItems)) {
                $this->leftItems[$parentItem]['items'][$id] = $item;
            }
        } elseif ($orientation === 'right') {
            if ($parentItem === 'sidebar') {
                $this->rightItems[$id] = $item;
            } elseif (array_key_exists($parentItem, $this->rightItems)) {
                $this->rightItems[$parentItem]['items'][$id] = $item;
            }
        }
    }

    /**
     * Set the name of the sidebar that will be shown when sidebar changed to vertical mode on small devices.
     * @param string $name New name of the sidebar.
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param array[] $items
     * @param string  $class
     * @return string
     */
    private function getSidebarHtml($items) {
        $sidebarNavHtml = '';
        $sidebarContentHtml = '';

        foreach ($items as $key => $menuEntry) {
            if (array_key_exists('items', $menuEntry) && is_array($menuEntry['items'])) {
                if (count($menuEntry['items']) === 1) {
                    // only one entry then add a simple link to the sidebar
                    $sidebarNavHtml .= $this->createHtmlLink(current($menuEntry['items']), false);
                } else {
                    $sidebarNavHtml .= $this->createHtmlLink($menuEntry, true);

                    // add a dropdown to the sidebar
                    $sidebarContentHtml .= '
                        <div class="' . $menuEntry['id'] . ' sticky-headline">
                            ' . $menuEntry['text'] . '
                        </div>
                
                        <div id="' . $menuEntry['id'] . '" class="' . $menuEntry['id'] . ' contentcontainer">
                            <ul>
                                <li class="sidebar_headline">' . $menuEntry['text'] . '</li>';

                    foreach ($menuEntry['items'] as $keyDropDown => $menuEntryDropDown) {
                        $sidebarContentHtml .= '    
                            <li>
                                ' . $this->createHtmlLink($menuEntryDropDown) . '
                            </li>';
                    }
                    $sidebarContentHtml .= '</ul></div>';
                }
            } else {
                // add a simple link to the sidebar
                $sidebarNavHtml .= $this->createHtmlLink($menuEntry, false);
            }
        }

        $returnArray = array();

        $returnArray['sidebarNav'] = '
            <div class="right-sidebar-navigation in-content" style="display: block;">
                <div class="right-sidebar-btn hidden-print">
                    <span class="glyphicon glyphicon-edit"> </span>
                </div>
                <div class="right-sidebar-link">
                    ' . $sidebarNavHtml . '
                </div>
            </div>';

        $returnArray['sidebarContent'] = '
            <div class="right-sidebar-close disabled-background-disable">
                <span class="glyphicon glyphicon-menu-right"></span>
            </div>

            <div class="right-sidebar-content" style="display: block;">            
                ' . $sidebarContentHtml . '
            </div>';

        return $returnArray;
    }

    /**
     * Creates the html output of the module menu. Each added menu item will be displayed.
     * If one item has several subitems than a dropdown button will be created.
     * @return string Returns the html output for the complete menu
     */
    public function show() {
        $showSidebar = false;
        $sidebarNavHtml = '';
        $sidebarContentHtml = '';

        // add left item block to sidebar
        if (count($this->leftItems) > 0) {
            $showSidebar = true;
            $sidebarNavHtml .= $this->getSidebarHtml($this->leftItems)['sidebarNav'];
            $sidebarContentHtml .= $this->getSidebarHtml($this->leftItems)['sidebarContent'];
        }

        // add form to sidebar
        if ($this->htmlForm !== '') {
            $showSidebar = true;
            $sidebarNavHtml .= $this->htmlForm;
        }

        // add right item block to sidebar
        if (count($this->rightItems) > 0) {
            $showSidebar = true;
            $sidebarNavHtml .= $this->getSidebarHtml($this->rightItems)['sidebarNav'];
            $sidebarContentHtml .= $this->getSidebarHtml($this->rightItems)['sidebarContent'];
        }

        if (!$showSidebar) {
            // dont show sidebar if no menu item or form was added
            return '';
        }

        // if sidebar will be shown then set this flag in page object
        if ($this->htmlPage instanceof \HtmlPage) {
            $this->htmlPage->hasNavbar();
        }

        // add html for sidebar
        $html .= '
            <div class="' . $this->customCssClass . '">
                ' . $sidebarNavHtml . '

                ' . $sidebarContentHtml . '
            </div>';

        // now show the complete html of the menu
        return $html;
    }
}