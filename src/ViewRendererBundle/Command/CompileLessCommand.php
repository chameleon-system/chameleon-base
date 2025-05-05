<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\Command;

use ChameleonSystem\ViewRendererBundle\objects\TPkgViewRendererLessCompiler;
use ChameleonSystem\ViewRendererBundle\Service\ThemeService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates Css from Less for all portals and writes it into appropriate files.
 */
#[AsCommand(name: 'chameleon_system:less:compile', description: 'Compiles LESS for all portals')]
class CompileLessCommand extends Command
{
    /**
     * @var TPkgViewRendererLessCompiler
     */
    private $lessCompiler;
    /**
     * @var ThemeService
     */
    private $themeService;

    public function __construct(TPkgViewRendererLessCompiler $lessCompiler, ThemeService $themeService)
    {
        parent::__construct('chameleon_system:less:compile');
        $this->lessCompiler = $lessCompiler;
        $this->themeService = $themeService;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDefinition([
                new InputOption('minify-css', '', InputOption::VALUE_NONE, 'Minify output css'),
            ])
            ->setHelp(<<<EOF
The <info>%command.name%</info> command compiles LESS for all portals:
<info>php %command.full_name% --minify-css</info>
NOTE: the source map (for debugging) is not written correctly when this command is used.
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Compiling less...');

        $portalList = \TdbCmsPortalList::GetList();
        $minifyCss = true === $input->getOption('minify-css');
        while ($portal = $portalList->Next()) {
            $this->themeService->setOverrideTheme($portal->GetFieldPkgCmsTheme());
            $css = $this->lessCompiler->getGeneratedCssForPortal($portal, $minifyCss);
            $this->themeService->setOverrideTheme(null);

            $cacheWriteSuccess = $this->lessCompiler->writeCssFileForPortal($css, $portal);

            if ($cacheWriteSuccess) {
                $output->writeln('<info>Compiled LESS for portal '.$portal->fieldName.'.<info>');
            } else {
                $output->writeln('<error>Failed compiling LESS for portal '.$portal->fieldName.'.</error>');
            }
        }
        $output->writeln('<info>Done.</info>');

        return 0;
    }
}
