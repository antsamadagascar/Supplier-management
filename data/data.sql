INSERT INTO `tabSupplier Group` (name, supplier_group_name, parent_supplier_group, creation, modified)
VALUES 
('SG-001', 'Matériel Informatique', 'Tous les groupes de fournisseurs', NOW(), NOW()),
('SG-002', 'Services Cloud', 'Tous les groupes de fournisseurs', NOW(), NOW()),
('SG-003', 'Équipement de Bureau', 'Tous les groupes de fournisseurs', NOW(), NOW());

INSERT INTO `tabItem` (name, item_code, item_name, item_group, stock_uom, is_stock_item, creation, modified)
VALUES 
('ITEM-001', 'SERV-CONS-001', 'Service de Consultation', 'Services', 'Heure', 0, NOW(), NOW()),
('ITEM-002', 'MATINFO-001', 'Serveur Dell PowerEdge', 'Matériel Informatique', 'Unité', 1, NOW(), NOW()),
('ITEM-003', 'LOG-ERP-001', 'Licence ERPNext', 'Logiciels', 'Unité', 0, NOW(), NOW());


INSERT INTO `tabSupplier` (name, supplier_name, supplier_group, supplier_type, country, creation, modified)
VALUES 
('SUP-001', 'TechSolutions SA', 'Matériel Informatique', 'Entreprise', 'France', NOW(), NOW()),
('SUl fkakjsdklfjhdP-002', 'CloudServe Inc.', 'Services Cloud', 'Entreprise', 'États-Unis', NOW(), NOW()),
('SUP-003', 'Bureau Équipement', 'Équipement de Bureau', 'Entreprise', 'Belgique', NOW(), NOW());

INSERT INTO `tabMaterial Request` (name, title, material_request_type, transaction_date, status, company, creation, modified, required_by)
VALUES 
('MAT-REQ-001', 'Demande de serveurs pour le département IT', 'Purchase', '2025-04-20', 'Pending', 'Votre Entreprise', NOW(), NOW(), '2025-05-15'),
('MAT-REQ-002', 'Acquisition de licences ERPNext', 'Purchase', '2025-04-22', 'Submitted', 'Votre Entreprise', NOW(), NOW(), '2025-05-20'),
('MAT-REQ-003', 'Services de consultation pour déploiement', 'Purchase', '2025-04-25', 'Ordered', 'Votre Entreprise', NOW(), NOW(), '2025-05-25');

INSERT INTO `tabMaterial Request Item` (name, parent, parentfield, parenttype, item_code, qty, schedule_date, creation, modified)
VALUES 
('MRI-001', 'MAT-REQ-001', 'items', 'Material Request', 'MATINFO-001', 5, '2025-05-05', NOW(), NOW()),
('MRI-002', 'MAT-REQ-002', 'items', 'Material Request', 'LOG-ERP-001', 10, '2025-05-10', NOW(), NOW()),
('MRI-003', 'MAT-REQ-003', 'items', 'Material Request', 'SERV-CONS-001', 40, '2025-05-15', NOW(), NOW());

INSERT INTO `tabSupplier Quotation` (name, supplier, transaction_date, valid_till, status, company, creation, modified)
VALUES 
('QUOT-001', 'SUP-001', '2025-04-21', '2025-05-21', 'Submitted', 'Votre Entreprise', NOW(), NOW()),
('QUOT-002', 'SUP-002', '2025-04-23', '2025-05-23', 'Submitted', 'Votre Entreprise', NOW(), NOW()),
('QUOT-003', 'SUP-003', '2025-04-26', '2025-05-26', 'Submitted', 'Votre Entreprise', NOW(), NOW());

INSERT INTO `tabSupplier Quotation Item` (name, parent, parentfield, parenttype, item_code, qty, rate, amount, creation, modified)
VALUES 
('SQI-001', 'QUOT-001', 'items', 'Supplier Quotation', 'MATINFO-001', 5, 2500, 12500, NOW(), NOW()),
('SQI-002', 'QUOT-002', 'items', 'Supplier Quotation', 'LOG-ERP-001', 10, 200, 2000, NOW(), NOW()),
('SQI-003', 'QUOT-003', 'items', 'Supplier Quotation', 'SERV-CONS-001', 40, 75, 3000, NOW(), NOW());

INSERT INTO `tabPurchase Order` (name, supplier, transaction_date, schedule_date, status, company, creation, modified)
VALUES 
('PO-001', 'SUP-001', '2025-04-25', '2025-05-10', 'To Receive and Bill', 'Votre Entreprise', NOW(), NOW()),
('PO-002', 'SUP-002', '2025-04-27', '2025-05-15', 'To Receive and Bill', 'Votre Entreprise', NOW(), NOW()),
('PO-003', 'SUP-003', '2025-04-30', '2025-05-20', 'To Receive and Bill', 'Votre Entreprise', NOW(), NOW());

INSERT INTO `tabPurchase Order Item` (name, parent, parentfield, parenttype, item_code, qty, rate, amount, schedule_date, creation, modified)
VALUES 
('POI-001', 'PO-001', 'items', 'Purchase Order', 'MATINFO-001', 5, 2450, 12250, '2025-05-10', NOW(), NOW()),
('POI-002', 'PO-002', 'items', 'Purchase Order', 'LOG-ERP-001', 10, 195, 1950, '2025-05-15', NOW(), NOW()),
('POI-003', 'PO-003', 'items', 'Purchase Order', 'SERV-CONS-001', 40, 70, 2800, '2025-05-20', NOW(), NOW());

INSERT INTO `tabPurchase Invoice` (name, supplier, posting_date, due_date, status, company, creation, modified)
VALUES 
('PINV-001', 'SUP-001', '2025-05-05', '2025-06-05', 'Paid', 'Votre Entreprise', NOW(), NOW()),
('PINV-002', 'SUP-002', '2025-05-10', '2025-06-10', 'Unpaid', 'Votre Entreprise', NOW(), NOW()),
('PINV-003', 'SUP-003', '2025-05-15', '2025-06-15', 'Unpaid', 'Votre Entreprise', NOW(), NOW());

INSERT INTO `tabPurchase Invoice Item` (name, parent, parentfield, parenttype, item_code, qty, rate, amount, creation, modified)
VALUES 
('PIVI-001', 'PINV-001', 'items', 'Purchase Invoice', 'MATINFO-001', 5, 2450, 12250, NOW(), NOW()),
('PIVI-002', 'PINV-002', 'items', 'Purchase Invoice', 'LOG-ERP-001', 10, 195, 1950, NOW(), NOW()),
('PIVI-003', 'PINV-003', 'items', 'Purchase Invoice', 'SERV-CONS-001', 40, 70, 2800, NOW(), NOW());


-- Devis clients (Quotation)
INSERT INTO `tabQuotation` (name, quotation_to, party_name, transaction_date, valid_till, order_type, status, company, creation, modified)
VALUES 
('QTN-001', 'Customer', 'CUST-001', '2025-04-15', '2025-05-15', 'Sales', 'Submitted', 'Votre Entreprise', NOW(), NOW()),
('QTN-002', 'Customer', 'CUST-002', '2025-04-18', '2025-05-18', 'Sales', 'Submitted', 'Votre Entreprise', NOW(), NOW()),
('QTN-003', 'Customer', 'CUST-003', '2025-04-22', '2025-05-22', 'Sales', 'Open', 'Votre Entreprise', NOW(), NOW());

-- Éléments des devis clients (Quotation Item)
INSERT INTO `tabQuotation Item` (name, parent, parentfield, parenttype, item_code, qty, rate, amount, creation, modified)
VALUES 
('QTI-001', 'QTN-001', 'items', 'Quotation', 'MATINFO-001', 2, 3200, 6400, NOW(), NOW()),
('QTI-002', 'QTN-002', 'items', 'Quotation', 'LOG-ERP-001', 5, 250, 1250, NOW(), NOW()),
('QTI-003', 'QTN-003', 'items', 'Quotation', 'SERV-CONS-001', 20, 95, 1900, NOW(), NOW());

-- Taxes et frais pour les devis clients
INSERT INTO `tabSales Taxes and Charges` (name, parent, parentfield, parenttype, charge_type, account_head, description, rate, tax_amount, creation, modified)
VALUES 
('ST-001', 'QTN-001', 'taxes', 'Quotation', 'On Net Total', 'TVA 20%', 'TVA', 20, 1280, NOW(), NOW()),
('ST-002', 'QTN-002', 'taxes', 'Quotation', 'On Net Total', 'TVA 20%', 'TVA', 20, 250, NOW(), NOW()),
('ST-003', 'QTN-003', 'taxes', 'Quotation', 'On Net Total', 'TVA 20%', 'TVA', 20, 380, NOW(), NOW());
-- Données clients (Customer)
INSERT INTO `tabCustomer` (name, customer_name, customer_type, customer_group, territory, creation, modified)
VALUES 
('CUST-001', 'Société Alpha', 'Company', 'Commercial', 'France', NOW(), NOW()),
('CUST-002', 'Entreprise Beta', 'Company', 'Commercial', 'Belgique', NOW(), NOW()),
('CUST-003', 'Organisation Gamma', 'Company', 'Government', 'Suisse', NOW(), NOW());

-- Coordonnées des clients (Customer Address)
INSERT INTO `tabAddress` (name, address_title, address_type, address_line1, city, state, country, is_primary_address, creation, modified)
VALUES 
('ADDR-001', 'Société Alpha-Siège', 'Billing', '15 Rue de la Paix', 'Paris', 'Île-de-France', 'France', 1, NOW(), NOW()),
('ADDR-002', 'Entreprise Beta-Siège', 'Billing', '25 Avenue Louise', 'Bruxelles', 'Bruxelles-Capitale', 'Belgique', 1, NOW(), NOW()),
('ADDR-003', 'Organisation Gamma-Siège', 'Billing', '10 Rue du Rhône', 'Genève', 'Genève', 'Suisse', 1, NOW(), NOW());

-- Liens entre clients et adresses
INSERT INTO `tabDynamic Link` (name, link_doctype, link_name, parent, parentfield, parenttype, creation, modified)
VALUES 
('DL-001', 'Customer', 'CUST-001', 'ADDR-001', 'links', 'Address', NOW(), NOW()),
('DL-002', 'Customer', 'CUST-002', 'ADDR-002', 'links', 'Address', NOW(), NOW()),
('DL-003', 'Customer', 'CUST-003', 'ADDR-003', 'links', 'Address', NOW(), NOW());