function showNotification(message, type = 'success') {
    console.log('Showing notification:', message);
    
    // Supprimer toute notification existante
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notification => notification.remove());
    
    // Créer la nouvelle notification
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white transition-opacity duration-300`;
    notification.textContent = message;
    
    // Ajouter la notification au DOM
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.style.opacity = '1';
    }, 100);
    
    // Animation de sortie et suppression
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// S'assurer que la fonction est disponible globalement
window.showNotification = showNotification; 