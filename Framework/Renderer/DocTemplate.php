<?php
namespace Framework\Renderer;

use Framework\Guard\UserInterface;
use League\Plates\Template\Template;

/**
 * Class DocTemplate
 * @package App\Renderer
 *
 * Utilisée pour l'autocompletion dans les templates
 *
 * @see AuthExtension
 * @method bool is_logged Si l'utilisateur est connecté
 * @method UserInterface current_user L'utilisateur actuellement connecté
 *
 * @see NeonExtension
 * @method array[] get_flashs La liste des messages flash en attente d'êtres affichés
 *
 * @see ManifestExtension
 * @method string resolve Résous un fichier en asset compilé
 * @method string asset alias de resolve
 *
 */
class DocTemplate extends Template {}
