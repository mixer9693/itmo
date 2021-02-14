<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    public static $STATIC_DIR = __DIR__.'/../../public/';
    public static $IMAGE_DIR = 'image/';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @var ?UploadedFile
     *
     * @Assert\File
     */
    private $file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile|null $file
     */
    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }

    public function getAbsolutePath(): ?string
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().$this->path;
    }

    public function getWebPath(): ?string
    {
        return null === $this->path
            ? null
            : '/'.$this->getUploadDir().$this->path;
    }

    protected function getUploadRootDir(): string
    {
        return self::$STATIC_DIR.$this->getUploadDir();
    }

    protected function getUploadDir(): string
    {
        return self::$IMAGE_DIR;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
        $this->getFile()->move($this->getUploadRootDir(), $this->path);
        $this->file = null;
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            $originalFilename = pathinfo($this->getFile()->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $this->path = $safeFilename.'-'.uniqid().'.'.$this->getFile()->guessExtension();
            $this->name = $this->getFile()->getClientOriginalName();
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }

}
