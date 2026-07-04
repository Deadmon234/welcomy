<?php
// Configuration des notifications WhatsApp.
// Cette configuration est utilisée chaque fois qu'une hôtesse marque un invité comme présent.

// URL de base pour l'envoi via WhatsApp. Laisser la valeur par défaut pour wa.me.
$WHATSAPP_BASE_URL = 'https://wa.me/';

// Numéro WhatsApp Asso+ utilisé pour le contact/support lors de la vérification des présences.
$WHATSAPP_SUPPORT_NUMBER = '+237 654143860';

// Modèle de message WhatsApp. Les variables suivantes sont supportées :
// {nom}, {event_title}, {event_date}, {event_location}, {support_phone}
$WHATSAPP_TEMPLATE = "Bonjour {nom},\n\nVotre présence à l'événement \"{event_title}\" est confirmée.\nDate: {event_date}\nLieu: {event_location}\n\nMerci de votre participation.\n\nPour tout problème lié à l'application Asso+, veuillez nous contacter au {support_phone}.";

// Message de remerciement envoyé après l'événement (variables : {nom}, {event_title}, {event_date}, {event_location}, {support_phone})
$WHATSAPP_THANKYOU_TEMPLATE = "Bonjour {nom},\n\nNous tenons à vous remercier chaleureusement pour votre participation à l'événement « {event_title} », qui s'est tenu le {event_date} à {event_location}.\n\nVotre présence a contribué à faire de cet événement un moment réussi. Nous espérons vous revoir très bientôt !\n\nPour toute question ou assistance concernant l'application Asso+, notre service client Asso+ est disponible au {support_phone}.\n\nEncore merci,\nL'équipe Asso+";

// Exemple :
// $WHATSAPP_TEMPLATE = "Bonjour {nom},\n\nVous êtes invité(e) à {event_title} le {event_date} à {event_location}.\nConfirmez votre présence via WhatsApp.";
