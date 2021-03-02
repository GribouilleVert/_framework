<?php
namespace Framework\Middlewares\Internals;

use Framework\Exceptions\SystemException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FileUploadErrorDetectionMiddleware implements MiddlewareInterface {

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var UploadedFileInterface $uploadedFile
         */
        foreach ($request->getUploadedFiles() as $uploadedFile) {
            switch ($uploadedFile->getError()) {
                case UPLOAD_ERR_NO_TMP_DIR:
                    throw new SystemException(trim(<<<EOT
                    Une erreur est survenue lors de l'envoie d'un fichier, merci de contacter votre
                    développeur/responsable informatique avec le code d'erreur suivant:
                    _FRAMEWORK/FileUpload/PhpErr:NoTmpDir
                    EOT), SystemException::SEVERITY_MEDIUM);

                case UPLOAD_ERR_CANT_WRITE:
                    throw new SystemException(trim(<<<EOT
                    Une erreur est survenue lors de l'envoie d'un fichier, merci de contacter votre
                    développeur/responsable informatique avec le code d'erreur suivant:
                    _FRAMEWORK/FileUpload/PhpErr:MissingWritePerm
                    EOT), SystemException::SEVERITY_MEDIUM);

                case UPLOAD_ERR_EXTENSION:
                    throw new SystemException(trim(<<<EOT
                    Une erreur est survenue lors de l'envoie d'un fichier, merci de contacter votre
                    développeur/responsable informatique avec le code d'erreur suivant:
                    _FRAMEWORK/FileUpload/PhpErr:ExtensionBlock
                    EOT), SystemException::SEVERITY_MEDIUM);
            }
        }

        return $handler->handle($request);
    }
}
