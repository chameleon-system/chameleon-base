<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCsv2sql {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /**  */
    public readonly string $name, 
    /** Column mapping */
    public readonly string $columnMapping, 
    /** File / directory */
    public readonly string $source, 
    /** Character set of the source file(s) */
    public readonly string $sourceCharset, 
    /** Target table */
    public readonly string $destinationTableName  ) {}
}