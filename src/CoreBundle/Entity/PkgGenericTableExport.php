<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgGenericTableExport {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name of the profile */
    public readonly string $name, 
    /** System name */
    public readonly string $systemName, 
    /** Query */
    public readonly string $restriction, 
    /** Template to be used (twig) */
    public readonly string $view, 
    /** Header template to be used (twig) */
    public readonly string $headerView, 
    /** Path to template */
    public readonly string $viewPath, 
    /** File name for export */
    public readonly string $exportFilename, 
    /** Source table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** Mapper configuration */
    public readonly string $mapperConfig, 
    /** Class */
    public readonly string $class, 
    /** Class path */
    public readonly string $classSubtype, 
    /** Class type */
    public readonly string $classType  ) {}
}