<?php

/**
 * **********************************************************************************************
 * List of all modules and administration pages of Admidio
 *
 * @copyright 2004-2017 The Admidio Team
 * @see https://www.admidio.org/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 * **********************************************************************************************
 */
// if config file doesn't exists, than show installation dialog
if (!is_file('../adm_my_files/config.php')) {
    header('Location: installation/index.php');
    exit();
}

function truncate($text, $chars = 250) {
    if (strlen($text) > $chars) {
        $showText = substr($text, 0, $chars);
        $showText = substr($showText, 0, strrpos($showText, ' '));
        $remainText = substr($text, strlen($showText));
        return array($showText, $remainText);
    } else {
        return array($text, '');
    }
}

require_once('../adm_program/system/common.php');

$headline = 'Home';

// Navigation of the module starts here
$gNavigation->addStartUrl(CURRENT_URL, $headline);

// create html page object
$page = new HtmlPage($headline);

$html = '
    <!-- Page Content -->
    <div class="container">

        <!-- Heading Row -->
        <div class="row">
            <div class="col-md-8">
                <img class="img-responsive img-rounded" src="http://placehold.it/900x350" alt="">
            </div>
            <!-- /.col-md-8 -->
            <div class="col-md-4">
                <h1>Business Name or Tagline</h1>
                <p>This is a template that is great for small businesses. It doesnt have too much fancy flare to it, but it makes a great use of the standard Bootstrap core components. Feel free to use this template for any project you want!</p>
                <a class="btn btn-primary btn-lg" href="#">Call to Action!</a>
            </div>
            <!-- /.col-md-4 -->
        </div>
        <!-- /.row -->

        <hr>

        <!-- Call to Action Well -->
        <div class="row">
            <div class="col-lg-12">
                <div class="well text-center">
                    This is a well that is a great spot for a business tagline or phone number for easy access!
                </div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <!-- Content Row -->
        <div class="row">';

$announcements = new ModuleAnnouncements();
/* $announcements->setParameter('id', $getId);
  $announcements->setParameter('cat_id', $getCatId);
  $announcements->setDateRange($getDateFrom, $getDateTo); */

// get all recordsets
$announcementsArray = $announcements->getDataSet($getStart, 3);
$announcement = new TableAnnouncement($gDb);

// show all announcements
foreach ($announcementsArray['recordset'] as $row) {
    $announcement->clear();
    $announcement->setArray($row);
    $announcementDesc = truncate($announcement->getValue('ann_description'));
    $html .= '
            <div class="col-md-4">
                <h2>' . $announcement->getValue('ann_headline') . '</h2>
                <div>' . $announcementDesc[0] . '</div><div id="ann' . $announcement->getValue('ann_id') . '" class="collapse">' . $announcementDesc[1] . '</div>';
    if (strlen($announcementDesc[1]) > 0) {
        $html .= '<a class="btn btn-default" href="#ann' . $announcement->getValue('ann_id') . '" data-toggle="collapse">More Info</a>';
    }
    $html .= '</div>';
}

$html .= '
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->';

$page->addHtml($html);

$page->show();
