<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{

  private $oldPassword;

  /**
   * @Assert\Regex(
   * pattern = "/^(?=.*\d)(?=.*[A-Z])(?=.*[@#$%-?+])(?!.*(.)\1{2}).*[a-z]/m",
   * match=true,
   * message="Votre mot de passe doit comporter au moins huit caractères, dont des lettres majuscules et minuscules, un chiffre et un symbole.")
   */
  private $newPassword;

  /**
   * @Assert\EqualTo(propertyPath="newPassword", message="Le mot de passe de confirmation ne correspond pas.")
   */
  private $confirmPassword;

  public function getOldPassword(): ?string
  {
    return $this->oldPassword;
  }

  public function setOldPassword(?string $oldPassword): self
  {
    $this->oldPassword = $oldPassword;

    return $this;
  }

  public function getNewPassword(): ?string
  {
    return $this->newPassword;
  }

  public function setNewPassword(?string $newPassword): self
  {
    $this->newPassword = $newPassword;

    return $this;
  }

  public function getConfirmPassword(): ?string
  {
    return $this->confirmPassword;
  }

  public function setConfirmPassword(?string $confirmPassword): self
  {
    $this->confirmPassword = $confirmPassword;

    return $this;
  }
}
