USE shopkamer;

-- Insertion des utilisateurs
INSERT INTO users (name, email, password, role) VALUES
('Alice Dupont', 'alice.dupont@example.com', 'Alice2026!', 'user'),
('Bruno Martin', 'bruno.martin@example.com', 'Bruno2026!', 'user'),
('Clara Admin', 'clara.admin@example.com', 'Admin2026!', 'admin');

-- Insertion des produits
INSERT INTO products (name, description, price, stock, category, image) VALUES
('Sac à dos urbain', 'Sac à dos résistant avec multiples compartiments pour ordinateur portable et accessoires.', 32500.00, 25, 'Bagagerie', 'images/sac-a-dos-urbain.avif'),
('Enceinte Bluetooth portable', 'Enceinte nomade avec 10 heures d''autonomie et son surround.', 52000.00, 18, 'Audio', 'images/enceinte-bluetooth.avif'),
('Montre connectée fitness', 'Montre intelligente avec suivi d''activité, rythme cardiaque et notifications.', 84500.00, 12, 'Wearables', 'images/montre-connectee.avif'),
('Jean slim homme', 'Jean coupe slim en denim extensible, idéal pour un look moderne.', 26000.00, 40, 'Mode', 'images/jean-slim-homme.avif'),
('T-shirt coton bio', 'T-shirt léger en coton biologique, disponible en plusieurs couleurs.', 13000.00, 50, 'Mode', 'images/tshirt-coton-bio.avif'),
('Lampe de bureau LED', 'Lampe design avec intensité réglable et chargeur sans fil intégré.', 22500.00, 30, 'Maison', 'images/lampe-led.avif'),
('Cafetière filtre', 'Cafetière filtre compacte avec arrêt automatique et pause service.', 39000.00, 22, 'Cuisine', 'images/cafetiere-filtre.avif'),
('Clé USB 64 Go', 'Clé USB 3.0 rapide pour stocker et transférer vos fichiers en toute sécurité.', 9500.00, 75, 'Informatique', 'images/cle-usb-64go.webp');
