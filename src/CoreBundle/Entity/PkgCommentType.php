<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCommentType {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Class to be used for pkg_comment */
    public readonly string $pkgCommentClassName, 
    /** Path to class for pkg_comment */
    public readonly string $pkgCommentClassSubType, 
    /** Table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** Class type for pkg_comment */
    public readonly string $pkgCommentClassType, 
    /** Class name */
    public readonly string $className, 
    /** Class subtype */
    public readonly string $classSubType, 
    /** Class type */
    public readonly string $classType  ) {}
}