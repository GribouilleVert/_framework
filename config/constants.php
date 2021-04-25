<?php

# If you enable production, the cache for the container and some other logic everywhere in the
# framework will trigger, but also potentially your in your code.
# Si vous activez le mode production, le cache du conteneur ainsi que d'autres logiques partout
# ailleur dans le framework s'activerons mais aussi potentiellement dans votre code.
const PRODUCTION = false;

# The name of the current app environment, currently defined by the PRODUCTION
# constant, but you may customise it.
# Le nom de l'environnement actuel de l'application, actuellement basée sur la
# constante PRODUCTION, vous pouvez la customiser.
const ENV = PRODUCTION ? 'production' : 'development';

# The release id, currently the _framework version, it is recommanded that you change it to
# the version number of your app like v4.5 or the commit short id list like a5f4b84d
# L'identifiant de publication, contient la version de _framework par défaut, il est recommandé
# de le changer, vous pouvez par exemple le remplacer par votre version d'application comme v4.5
# ou par un identifiant cours de commit comme a5f4b84d
const RELEASE = 'v1.3';

# Used with HttpsMiddleware, forces the use of https
# Utilisé avec le HttpsMiddleware, force l'utilisation du https
const REQUIRE_HTTPS = PRODUCTION;


# For logging error with sentry, set to null to disable sentry
# Pour logger les erreurs avec sentry, mettez à null pour désactiver sentry
const SENTRY_DSN = null;

# To make sentry catch all error (declare a global wrapper
# when vendor/autoload.php is included)
# Pour faire en sorte que sentry attrape toutes les erreurs (declare un
# wrapper global quand le vendor/autoload.php est inclus)
const SENTRY_ALL = false;
