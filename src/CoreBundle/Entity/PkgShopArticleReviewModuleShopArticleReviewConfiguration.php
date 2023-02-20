<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgShopArticleReviewModuleShopArticleReviewConfiguration {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Number of evaluation credits */
private string $ratingCount = '5', 
    // TCMSFieldVarchar
/** @var string - Show number of reviews */
private string $countShowReviews = '3', 
    // TCMSFieldVarchar
/** @var string - Heading */
private string $title = ''  ) {}

  public function getId(): string
  {
    return $this->id;
  }
  public function setId(string $id): self
  {
    $this->id = $id;
    return $this;
  }

  public function getCmsident(): ?int
  {
    return $this->cmsident;
  }
  public function setCmsident(int $cmsident): self
  {
    $this->cmsident = $cmsident;
    return $this;
  }
    // TCMSFieldVarchar
public function getRatingCount(): string
{
    return $this->ratingCount;
}
public function setRatingCount(string $ratingCount): self
{
    $this->ratingCount = $ratingCount;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCountShowReviews(): string
{
    return $this->countShowReviews;
}
public function setCountShowReviews(string $countShowReviews): self
{
    $this->countShowReviews = $countShowReviews;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTitle(): string
{
    return $this->title;
}
public function setTitle(string $title): self
{
    $this->title = $title;

    return $this;
}


  
}
