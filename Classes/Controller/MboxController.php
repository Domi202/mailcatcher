<?php
namespace Domi202\Mailcatcher\Controller;

use Domi202\Mailcatcher\Parser\MboxParser;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

class MboxController extends ActionController
{
    /**
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @param ViewInterface $view
     */
    public function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);
        $this->generateMenu();
    }

    /**
     * @return void
     */
    protected function generateMenu()
    {
        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('mboxSelection');

        $menuItem = $menu
            ->makeMenuItem()
            ->setHref(
                $this->uriBuilder->reset()->uriFor('index')
            )
            ->setTitle('Overview');
        $menu->addMenuItem($menuItem);

        $files = $this->getMboxFiles();
        foreach ($files as $filename => $path) {
            $menuItem = $menu
                ->makeMenuItem()
                ->setHref(
                    $this->uriBuilder->reset()->uriFor('view', ['path' => $path])
                )
                ->setTitle($filename);

            if ($this->arguments->hasArgument('path')) {
                if ($this->arguments->getArgument('path')->getValue() === $path) {
                    $menuItem->setActive(true);
                }
            }

            $menu->addMenuItem($menuItem);
        }

        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /**
     * @return array
     */
    protected function getMboxFiles(): array
    {
        $files = [];
        foreach (new \DirectoryIterator(PATH_site . 'typo3temp/var/mail') as $fileInfo) {
            if ($fileInfo->isDot()
                || $fileInfo->isDir()
            ) {
                continue;
            }
            if ($fileInfo->getExtension() == 'mbox') {
                $files[$fileInfo->getFilename()] = $fileInfo->getRealPath();
            }
        }
        return $files;
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $files = $this->getMboxFiles();
        $this->view->assign('files', $files);
    }

    /**
     * @param string $path
     * @return void
     */
    public function viewAction(string $path)
    {
        $mbox = new MboxParser($path);
        $mbox->processFile();
        $this->view->assign('mails', $mbox->getMails());
    }
}
