<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table;

use PhpMyAdmin\Common;
use PhpMyAdmin\Config\PageSettings;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Response;
use PhpMyAdmin\SqlQueryForm;
use PhpMyAdmin\Template;
use PhpMyAdmin\Url;
use function htmlspecialchars;

/**
 * Table SQL executor
 */
final class SqlController extends AbstractController
{
    /** @var SqlQueryForm */
    private $sqlQueryForm;

    /**
     * @param Response          $response     A Response instance.
     * @param DatabaseInterface $dbi          A DatabaseInterface instance.
     * @param Template          $template     A Template instance.
     * @param string            $db           Database name.
     * @param string            $table        Table name.
     * @param SqlQueryForm      $sqlQueryForm An SqlQueryForm instance.
     */
    public function __construct($response, $dbi, Template $template, $db, $table, SqlQueryForm $sqlQueryForm)
    {
        parent::__construct($response, $dbi, $template, $db, $table);
        $this->sqlQueryForm = $sqlQueryForm;
    }

    public function index(): void
    {
        global $err_url, $goto, $back;

        $this->addScriptFiles([
            'makegrid.js',
            'vendor/jquery/jquery.uitablefilter.js',
            'vendor/stickyfill.min.js',
            'sql.js',
        ]);

        $pageSettings = new PageSettings('Sql');
        $this->response->addHTML($pageSettings->getErrorHTML());
        $this->response->addHTML($pageSettings->getHTML());

        Common::table();

        $err_url = Url::getFromRoute('/table/sql') . $err_url;

        /**
         * After a syntax error, we return to this script
         * with the typed query in the textarea.
         */
        $goto = Url::getFromRoute('/table/sql');
        $back = Url::getFromRoute('/table/sql');

        $this->response->addHTML($this->sqlQueryForm->getHtml(
            $_GET['sql_query'] ?? true,
            false,
            isset($_POST['delimiter'])
                ? htmlspecialchars($_POST['delimiter'])
                : ';'
        ));
    }
}
