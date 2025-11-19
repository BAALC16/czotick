-- ============================================
-- REQUÊTES SQL POUR TESTER LES DIFFÉRENTS CAS DE PAIEMENT
-- Événement: premium-bool (id=5)
-- Ticket Type: Entrée standard (id=1, prix=30000 XOF)
-- ============================================

USE org_jci_abidjan_ivoire;

-- ============================================
-- CAS 1: PAIEMENT PARTIEL (partial)
-- Montant payé: 4000 XOF (partial_payment_amount)
-- Solde restant: 26000 XOF
-- ============================================
INSERT INTO `registrations` (
    `event_id`,
    `ticket_type_id`,
    `registration_number`,
    `fullname`,
    `phone`,
    `email`,
    `organization`,
    `position`,
    `ticket_price`,
    `amount_paid`,
    `currency`,
    `status`,
    `payment_status`,
    `registration_date`,
    `form_data`,
    `created_at`,
    `updated_at`
) VALUES (
    5,
    1,
    'REG-PB-001',
    'Jean Dupont',
    '+2250701234567',
    'jean.dupont@example.com',
    'JCI Abidjan',
    'Membre',
    30000.00,
    4000.00,
    'XOF',
    'pending',
    'partial',
    NOW(),
    '{"fullname":"Jean Dupont","email":"jean.dupont@example.com","phone":"+2250701234567","organization":"JCI Abidjan","position":"Membre"}',
    NOW(),
    NOW()
);

-- Transaction de paiement partiel
INSERT INTO `payment_transactions` (
    `registration_id`,
    `transaction_reference`,
    `amount`,
    `currency`,
    `payment_method`,
    `payment_provider`,
    `status`,
    `payment_date`,
    `processed_date`,
    `metadata`,
    `created_at`,
    `updated_at`
) VALUES (
    LAST_INSERT_ID(),
    'TXN-PB-001',
    4000.00,
    'XOF',
    'mobile_money',
    'Orange Money',
    'completed',
    NOW(),
    NOW(),
    '{"participant_data":{"fullname":"Jean Dupont","email":"jean.dupont@example.com","phone":"+2250701234567","ticket_type_id":1,"is_partial_completion":false},"event_data":{"event_id":5}}',
    NOW(),
    NOW()
);

-- ============================================
-- CAS 2: PAIEMENT PARTIEL AVANCÉ (partial)
-- Montant payé: 15000 XOF (50% du ticket)
-- Solde restant: 15000 XOF
-- ============================================
INSERT INTO `registrations` (
    `event_id`,
    `ticket_type_id`,
    `registration_number`,
    `fullname`,
    `phone`,
    `email`,
    `organization`,
    `position`,
    `ticket_price`,
    `amount_paid`,
    `currency`,
    `status`,
    `payment_status`,
    `registration_date`,
    `form_data`,
    `created_at`,
    `updated_at`
) VALUES (
    5,
    1,
    'REG-PB-002',
    'Marie Kouassi',
    '+2250509876543',
    'marie.kouassi@example.com',
    'JCI Yopougon',
    'Présidente',
    30000.00,
    15000.00,
    'XOF',
    'pending',
    'partial',
    NOW(),
    '{"fullname":"Marie Kouassi","email":"marie.kouassi@example.com","phone":"+2250509876543","organization":"JCI Yopougon","position":"Présidente"}',
    NOW(),
    NOW()
);

-- Transaction de paiement partiel
INSERT INTO `payment_transactions` (
    `registration_id`,
    `transaction_reference`,
    `amount`,
    `currency`,
    `payment_method`,
    `payment_provider`,
    `status`,
    `payment_date`,
    `processed_date`,
    `metadata`,
    `created_at`,
    `updated_at`
) VALUES (
    LAST_INSERT_ID(),
    'TXN-PB-002',
    15000.00,
    'XOF',
    'mobile_money',
    'MTN Money',
    'completed',
    NOW(),
    NOW(),
    '{"participant_data":{"fullname":"Marie Kouassi","email":"marie.kouassi@example.com","phone":"+2250509876543","ticket_type_id":1,"is_partial_completion":false},"event_data":{"event_id":5}}',
    NOW(),
    NOW()
);

-- ============================================
-- CAS 3: RÉSERVATION (reservation)
-- Montant payé: 2000 XOF (reservation_amount si activé)
-- Note: Pour cet événement, allow_reservation=0, mais on crée quand même un cas de test
-- ============================================
INSERT INTO `registrations` (
    `event_id`,
    `ticket_type_id`,
    `registration_number`,
    `fullname`,
    `phone`,
    `email`,
    `organization`,
    `position`,
    `ticket_price`,
    `amount_paid`,
    `currency`,
    `status`,
    `payment_status`,
    `registration_date`,
    `form_data`,
    `created_at`,
    `updated_at`
) VALUES (
    5,
    1,
    'REG-PB-003',
    'Paul Traoré',
    '+2250102345678',
    'paul.traore@example.com',
    'JCI Cocody',
    'Trésorier',
    30000.00,
    2000.00,
    'XOF',
    'pending',
    'reservation',
    NOW(),
    '{"fullname":"Paul Traoré","email":"paul.traore@example.com","phone":"+2250102345678","organization":"JCI Cocody","position":"Trésorier"}',
    NOW(),
    NOW()
);

-- Transaction de réservation
INSERT INTO `payment_transactions` (
    `registration_id`,
    `transaction_reference`,
    `amount`,
    `currency`,
    `payment_method`,
    `payment_provider`,
    `status`,
    `payment_date`,
    `processed_date`,
    `metadata`,
    `created_at`,
    `updated_at`
) VALUES (
    LAST_INSERT_ID(),
    'TXN-PB-003',
    2000.00,
    'XOF',
    'mobile_money',
    'Moov Money',
    'completed',
    NOW(),
    NOW(),
    '{"participant_data":{"fullname":"Paul Traoré","email":"paul.traore@example.com","phone":"+2250102345678","ticket_type_id":1,"is_partial_completion":false},"event_data":{"event_id":5}}',
    NOW(),
    NOW()
);

-- ============================================
-- CAS 4: PAIEMENT COMPLET (paid)
-- Montant payé: 30000 XOF (100% du ticket)
-- ============================================
INSERT INTO `registrations` (
    `event_id`,
    `ticket_type_id`,
    `registration_number`,
    `fullname`,
    `phone`,
    `email`,
    `organization`,
    `position`,
    `ticket_price`,
    `amount_paid`,
    `currency`,
    `status`,
    `payment_status`,
    `registration_date`,
    `confirmation_date`,
    `form_data`,
    `created_at`,
    `updated_at`
) VALUES (
    5,
    1,
    'REG-PB-004',
    'Sophie Diallo',
    '+2250908765432',
    'sophie.diallo@example.com',
    'JCI Plateau',
    'Secrétaire',
    30000.00,
    30000.00,
    'XOF',
    'confirmed',
    'paid',
    NOW(),
    NOW(),
    '{"fullname":"Sophie Diallo","email":"sophie.diallo@example.com","phone":"+2250908765432","organization":"JCI Plateau","position":"Secrétaire"}',
    NOW(),
    NOW()
);

-- Transaction de paiement complet
INSERT INTO `payment_transactions` (
    `registration_id`,
    `transaction_reference`,
    `amount`,
    `currency`,
    `payment_method`,
    `payment_provider`,
    `status`,
    `payment_date`,
    `processed_date`,
    `metadata`,
    `created_at`,
    `updated_at`
) VALUES (
    LAST_INSERT_ID(),
    'TXN-PB-004',
    30000.00,
    'XOF',
    'mobile_money',
    'Wave',
    'completed',
    NOW(),
    NOW(),
    '{"participant_data":{"fullname":"Sophie Diallo","email":"sophie.diallo@example.com","phone":"+2250908765432","ticket_type_id":1,"is_partial_completion":false},"event_data":{"event_id":5}}',
    NOW(),
    NOW()
);

-- ============================================
-- CAS 5: PAIEMENT EN ATTENTE (pending)
-- Montant payé: 0 XOF
-- ============================================
INSERT INTO `registrations` (
    `event_id`,
    `ticket_type_id`,
    `registration_number`,
    `fullname`,
    `phone`,
    `email`,
    `organization`,
    `position`,
    `ticket_price`,
    `amount_paid`,
    `currency`,
    `status`,
    `payment_status`,
    `registration_date`,
    `form_data`,
    `created_at`,
    `updated_at`
) VALUES (
    5,
    1,
    'REG-PB-005',
    'Amadou Koné',
    '+2250304567890',
    'amadou.kone@example.com',
    'JCI Marcory',
    'Membre',
    30000.00,
    0.00,
    'XOF',
    'pending',
    'pending',
    NOW(),
    '{"fullname":"Amadou Koné","email":"amadou.kone@example.com","phone":"+2250304567890","organization":"JCI Marcory","position":"Membre"}',
    NOW(),
    NOW()
);

-- ============================================
-- CAS 6: PAIEMENT ÉCHOUÉ (failed)
-- Montant payé: 0 XOF
-- ============================================
INSERT INTO `registrations` (
    `event_id`,
    `ticket_type_id`,
    `registration_number`,
    `fullname`,
    `phone`,
    `email`,
    `organization`,
    `position`,
    `ticket_price`,
    `amount_paid`,
    `currency`,
    `status`,
    `payment_status`,
    `registration_date`,
    `form_data`,
    `created_at`,
    `updated_at`
) VALUES (
    5,
    1,
    'REG-PB-006',
    'Fatou Soro',
    '+2250807654321',
    'fatou.soro@example.com',
    'JCI Adjamé',
    'Membre',
    30000.00,
    0.00,
    'XOF',
    'pending',
    'failed',
    NOW(),
    '{"fullname":"Fatou Soro","email":"fatou.soro@example.com","phone":"+2250807654321","organization":"JCI Adjamé","position":"Membre"}',
    NOW(),
    NOW()
);

-- Transaction de paiement échoué
INSERT INTO `payment_transactions` (
    `registration_id`,
    `transaction_reference`,
    `amount`,
    `currency`,
    `payment_method`,
    `payment_provider`,
    `status`,
    `payment_date`,
    `processed_date`,
    `metadata`,
    `created_at`,
    `updated_at`
) VALUES (
    LAST_INSERT_ID(),
    'TXN-PB-006',
    30000.00,
    'XOF',
    'mobile_money',
    'Orange Money',
    'failed',
    NOW(),
    NOW(),
    '{"participant_data":{"fullname":"Fatou Soro","email":"fatou.soro@example.com","phone":"+2250807654321","ticket_type_id":1},"event_data":{"event_id":5},"error":"Solde insuffisant"}',
    NOW(),
    NOW()
);

-- ============================================
-- CAS 7: PAIEMENT PARTIEL MULTIPLE (partial)
-- Premier paiement: 5000 XOF
-- Deuxième paiement: 10000 XOF (sera ajouté via finalisation)
-- Total payé: 15000 XOF
-- Solde restant: 15000 XOF
-- ============================================
INSERT INTO `registrations` (
    `event_id`,
    `ticket_type_id`,
    `registration_number`,
    `fullname`,
    `phone`,
    `email`,
    `organization`,
    `position`,
    `ticket_price`,
    `amount_paid`,
    `currency`,
    `status`,
    `payment_status`,
    `registration_date`,
    `form_data`,
    `created_at`,
    `updated_at`
) VALUES (
    5,
    1,
    'REG-PB-007',
    'Yves Bamba',
    '+2250405678901',
    'yves.bamba@example.com',
    'JCI Treichville',
    'Vice-Président',
    30000.00,
    5000.00,
    'XOF',
    'pending',
    'partial',
    NOW(),
    '{"fullname":"Yves Bamba","email":"yves.bamba@example.com","phone":"+2250405678901","organization":"JCI Treichville","position":"Vice-Président"}',
    NOW(),
    NOW()
);

-- Première transaction de paiement partiel
INSERT INTO `payment_transactions` (
    `registration_id`,
    `transaction_reference`,
    `amount`,
    `currency`,
    `payment_method`,
    `payment_provider`,
    `status`,
    `payment_date`,
    `processed_date`,
    `metadata`,
    `created_at`,
    `updated_at`
) VALUES (
    LAST_INSERT_ID(),
    'TXN-PB-007-1',
    5000.00,
    'XOF',
    'mobile_money',
    'Orange Money',
    'completed',
    NOW(),
    NOW(),
    '{"participant_data":{"fullname":"Yves Bamba","email":"yves.bamba@example.com","phone":"+2250405678901","ticket_type_id":1,"is_partial_completion":false},"event_data":{"event_id":5}}',
    NOW(),
    NOW()
);

-- ============================================
-- CAS 8: PAIEMENT PARTIEL PRESQUE COMPLET (partial)
-- Montant payé: 29000 XOF (97% du ticket)
-- Solde restant: 1000 XOF
-- ============================================
INSERT INTO `registrations` (
    `event_id`,
    `ticket_type_id`,
    `registration_number`,
    `fullname`,
    `phone`,
    `email`,
    `organization`,
    `position`,
    `ticket_price`,
    `amount_paid`,
    `currency`,
    `status`,
    `payment_status`,
    `registration_date`,
    `form_data`,
    `created_at`,
    `updated_at`
) VALUES (
    5,
    1,
    'REG-PB-008',
    'Koffi N\'Guessan',
    '+2250606789012',
    'koffi.nguessan@example.com',
    'JCI Abobo',
    'Membre',
    30000.00,
    29000.00,
    'XOF',
    'pending',
    'partial',
    NOW(),
    '{"fullname":"Koffi N\'Guessan","email":"koffi.nguessan@example.com","phone":"+2250606789012","organization":"JCI Abobo","position":"Membre"}',
    NOW(),
    NOW()
);

-- Transaction de paiement partiel
INSERT INTO `payment_transactions` (
    `registration_id`,
    `transaction_reference`,
    `amount`,
    `currency`,
    `payment_method`,
    `payment_provider`,
    `status`,
    `payment_date`,
    `processed_date`,
    `metadata`,
    `created_at`,
    `updated_at`
) VALUES (
    LAST_INSERT_ID(),
    'TXN-PB-008',
    29000.00,
    'XOF',
    'mobile_money',
    'MTN Money',
    'completed',
    NOW(),
    NOW(),
    '{"participant_data":{"fullname":"Koffi N\'Guessan","email":"koffi.nguessan@example.com","phone":"+2250606789012","ticket_type_id":1,"is_partial_completion":false},"event_data":{"event_id":5}}',
    NOW(),
    NOW()
);

-- ============================================
-- VÉRIFICATION DES DONNÉES CRÉÉES
-- ============================================
SELECT
    r.id,
    r.registration_number,
    r.fullname,
    r.email,
    r.phone,
    r.ticket_price,
    r.amount_paid,
    (r.ticket_price - r.amount_paid) as balance_due,
    r.payment_status,
    r.status,
    r.registration_date
FROM registrations r
WHERE r.event_id = 5
ORDER BY r.id DESC;

-- ============================================
-- STATISTIQUES DES PAIEMENTS
-- ============================================
SELECT
    payment_status,
    COUNT(*) as count,
    SUM(amount_paid) as total_paid,
    AVG(amount_paid) as avg_paid,
    SUM(ticket_price - amount_paid) as total_balance_due
FROM registrations
WHERE event_id = 5
GROUP BY payment_status;

