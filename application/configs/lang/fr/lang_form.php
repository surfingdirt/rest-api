<?php
$form = array(
  // Zend_Validate messages
  Zend_Validate_Alnum::NOT_ALNUM => 'ne peut contenir que des lettres ou des chiffres',
  Zend_Validate_Alnum::STRING_EMPTY => 'ne peut être vide',
  Zend_Validate_Alpha::NOT_ALPHA => 'ne peut contenir que des lettres',
  Zend_Validate_Alpha::STRING_EMPTY => 'ne peut être vide',
  Zend_Validate_Between::NOT_BETWEEN => "plage autorisée: [%min%, %max%]",
  Zend_Validate_Between::NOT_BETWEEN_STRICT => "plage autorisée: ]%min%, %max%[",
  Zend_Validate_Ccnum::LENGTH => 'doit contenir entre 13 et 19 chiffres',
  Zend_Validate_Ccnum::CHECKSUM => 'l\'algorithme Luhn a échoué',
  Zend_Validate_Date::NOT_YYYY_MM_DD => 'n\'est pas au format AAAA-MM-JJ',
  Zend_Validate_Date::INVALID => 'n\'est pas une date valide',
  Zend_Validate_Date::FALSEFORMAT => 'non conforme au format de date donné',
  Zend_Validate_Digits::NOT_DIGITS => 'ne doit contenir que des chiffres',
  Zend_Validate_Digits::STRING_EMPTY => 'ne peut être vide',
  Zend_Validate_EmailAddress::INVALID => 'email invalide',
  Zend_Validate_EmailAddress::INVALID_HOSTNAME => 'nom d\'hôte invalide',
  Zend_Validate_EmailAddress::INVALID_MX_RECORD => "'%hostname%'n'a pas de MX pour cet email",
  Zend_Validate_EmailAddress::DOT_ATOM => 'non conforme au format dot-atom',
  Zend_Validate_EmailAddress::QUOTED_STRING => 'non conforme au format quoted-string',
  Zend_Validate_EmailAddress::INVALID_LOCAL_PART => "'%localPart%' est un nom d\\'utilisateur invalide",
  Zend_Validate_Float::NOT_FLOAT => 'doit être un nombre à virgule',
  Zend_Validate_GreaterThan::NOT_GREATER => "doit être supérieur à '%min'",
  Zend_Validate_Hex::NOT_HEX => 'ne doit contenir que des caractères hexadécimaux',
  Zend_Validate_Hostname::IP_ADDRESS_NOT_ALLOWED => "'%value% est une adresse IP, pas un nom d\\'hôte'",
  Zend_Validate_Hostname::UNKNOWN_TLD => 'le TLD n\'est pas reconnu',
  Zend_Validate_Hostname::INVALID_DASH => '"-" est interdit',
  Zend_Validate_Hostname::INVALID_HOSTNAME_SCHEMA => 'mauvais format',
  Zend_Validate_Hostname::UNDECIPHERABLE_TLD => 'TLD indéchiffrable',
  Zend_Validate_Hostname::INVALID_HOSTNAME => 'nom d\'hôte invalide',
  Zend_Validate_Hostname::INVALID_LOCAL_NAME => 'nom de réseau local invalide',
  Zend_Validate_Hostname::LOCAL_NAME_NOT_ALLOWED => 'ne doit pas être un nom de réseau local',
  Zend_Validate_Identical::NOT_SAME => 'les deux champs sont différents',
  Zend_Validate_Identical::MISSING_TOKEN => 'élément manquant pour la comparaison',
  Lib_Validate_CSRF::NOT_SAME_2 => 'action non autorisée, merci de recharger la page',
  Lib_Validate_CSRF::MISSING_TOKEN_2 => 'autorisation introuvable, merci de recharger la page',
  Zend_Validate_InArray::NOT_IN_ARRAY => 'doit être contenu dans la liste',
  Zend_Validate_Int::NOT_INT => 'doit être un nombre entier',
  Zend_Validate_Ip::NOT_IP_ADDRESS => 'doit être une adresse IP valide',
  Zend_Validate_LessThan::NOT_LESS => "'doit être inférieur à '%max%'",
  Zend_Validate_NotEmpty::IS_EMPTY => 'ne peut être vide',
  Zend_Validate_Regex::NOT_MATCH => 'mauvais format',
  Zend_Validate_StringLength::TOO_SHORT => 'trop court',
  Zend_Validate_StringLength::TOO_LONG => 'trop long',
  Lib_Validate_IdenticalTo::MISSING_REFERENCE => 'pas de référence',
  Lib_Validate_IdenticalTo::NOT_SAME => 'les deux champs doivent être identiques',
  Lib_Validate_File_Upload::INI_SIZE => "fichier trop gros",
  Lib_Validate_File_Upload::FORM_SIZE => "fichier trop gros",
  Lib_Validate_File_Upload::PARTIAL => "le fichier n'a été envoyé que partiellement",
  Lib_Validate_File_Upload::NO_FILE => "le fichier n'a pas été envoyé",
  Lib_Validate_File_Upload::NO_TMP_DIR => "pas de répertoire temporaire pour l'envoi de fichier",
  Lib_Validate_File_Upload::CANT_WRITE => "le fichier n'a pas pu être écrit",
  Lib_Validate_File_Upload::EXTENSION => "l'extension a retourné une erreur pendant l'envoi de fichier",
  Lib_Validate_File_Upload::ATTACK => "possible attaque",
  Lib_Validate_File_Upload::FILE_NOT_FOUND => "le fichier est introuvable",
  Lib_Validate_File_Upload::UNKNOWN => "une erreur est apparue pendant l'envoi de fichier",
  Zend_Validate_File_Extension::FALSE_EXTENSION => 'mauvaise extension de fichier',
  Zend_Validate_File_Extension::NOT_FOUND => 'fichier sans extension',
  Zend_Validate_File_MimeType::FALSE_TYPE => 'mauvais type de fichier',
  Lib_Validate_Video::NOT_VALID => 'le code de cette vidéo n\'est pas valide',
  Lib_Validate_Username::SELF_UNALLOWED => 'tu ne peux pas choisir ton propre nom',
  Lib_Validate_TrickQuestion::WRONGANSWER => 'mauvaise réponse!',
  Lib_Validate_LocationRequired::LOCATION_REQUIRED => 'indique l\'endroit sur la carte',
  'searchSubmit' => 'rechercher',
);