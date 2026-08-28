<?php

declare(strict_types=1);

return [
    'refusal' => 'Non ho trovato questa informazione nei documenti indicizzati.',

    'mcp' => [
        'instructions' => <<<'TEXT'
            Questo server espone una base di conoscenza privata e indicizzata tramite ricerca semantica.

            Flusso consigliato:
              1. Leggi prima la risorsa "documents" per scoprire quali documenti esistono e
                 quali sono i loro identificativi.
              2. Chiama il tool di ricerca con una domanda in linguaggio naturale, restringendo
                 per sorgente, id del documento o intervallo di posizioni quando sai gia dove cercare.
              3. Usa il tool di lettura per ottenere un passaggio contiguo attorno a un risultato promettente.

            Ogni risultato riporta il documento di provenienza e l'intervallo di posizioni: citali
            quando riferisci una risposta. Il corpus puo contenere errori di OCR: riporta cio che
            il testo dice, senza correggerlo.
            TEXT,
        'search_description' => 'Ricerca semantica nella base di conoscenza indicizzata. Restituisce i passaggi piu rilevanti con documento e posizione.',
        'fetch_description' => 'Legge un intervallo contiguo di un documento indicizzato per posizione (ad esempio un intervallo di pagine).',
        'answer_description' => 'Risponde a una domanda usando la base di conoscenza, con risposta ancorata e citazioni.',
        'documents_description' => 'I documenti attualmente indicizzati, con identificativi, titoli e copertura.',
    ],

    'chat' => [
        'title' => 'Chat',
        'untitled' => 'Chat senza titolo',
        'new_chat' => 'Nuova chat',
        'search_placeholder' => 'Cerca nelle chat',
        'toggle_theme' => 'Cambia tema',
        'toggle_sidebar' => 'Mostra o nascondi la barra laterale',
        'settings' => 'Impostazioni avanzate',
        'settings_sub' => 'Non serve toccare nulla di tutto questo per fare una domanda. Cambialo solo se le risposte non trovano quello che cerchi.',
        'model' => 'Modello',
        'model_help' => 'Un modello più grande ragiona meglio e costa di più per ogni risposta.',
        'sources_field' => 'Dove cercare',
        'sources_help' => 'Lascia tutto deselezionato per cercare in tutta la base documentale.',
        'top_k' => 'Passaggi recuperati',
        'top_k_help' => 'Quanti estratti vengono passati al modello. Più contesto, più token.',
        'min_score' => 'Somiglianza minima',
        'min_score_help' => 'Gli estratti sotto questa soglia vengono scartati prima che il modello li veda.',
        'retrieval_only' => 'Solo ricerca, senza risposta scritta',
        'retrieval_only_help' => 'Mostra i passaggi che corrispondono senza chiamare il modello. Nessun costo.',
        'retrieval_only_answer' => '{0}Nessun passaggio corrispondente.|{1}Un passaggio corrispondente. Apri le fonti per leggerlo.|[2,*]:count passaggi corrispondenti. Apri le fonti per leggerli.',
        'sources' => 'Fonti',
        'close' => 'Chiudi',
        'reset' => 'Ripristina',
        'cancel' => 'Annulla',
        'save' => 'Salva',
        'send' => 'Invia',
        'placeholder' => 'Fai una domanda sui documenti...',
        'hint' => 'Le risposte vengono dai documenti indicizzati e citano sempre il passaggio da cui provengono.',
        'empty_title' => 'Cosa vuoi sapere?',
        'empty_body' => 'Chiedi con parole tue. Le domande complete funzionano molto meglio delle parole chiave.',
        'suggestions' => [],

        // Renderizzate nel browser da rag-chat.js.
        'js' => [
            'untitled' => 'Chat senza titolo',
            'newChat' => 'Nuova chat',
            'noThreads' => 'Nessuna chat',
            'noResults' => 'Nessun risultato',
            'totalSpend' => 'Totale',
            'copy' => 'Copia',
            'sources' => 'Fonti',
            'helpful' => 'Utile',
            'notHelpful' => 'Non utile',
            'showMore' => 'Mostra tutto il passaggio',
            'showLess' => 'Mostra meno',
            'openSource' => 'Apri la fonte',
            'passageCount' => ':total passaggi, :cited citati',
            'refusalHint' => 'Prova a riformulare la domanda, oppure allarga le fonti dalle impostazioni avanzate.',
            'failed' => 'La richiesta non è riuscita.',
            'stopped' => 'Interrotta.',
            'stop' => 'Interrompi',
            'send' => 'Invia',
            'confirmDelete' => 'Eliminare questa chat e le sue risposte?',
            'rename' => 'Rinomina',
            'pin' => 'Fissa in alto',
            'delete' => 'Elimina',
            'allSources' => 'Tutte le fonti',
            'defaultModel' => 'Modello predefinito',
            'retrievalOnly' => 'Solo ricerca',
            'groups' => [
                'pinned' => 'Fissate',
                'today' => 'Oggi',
                'yesterday' => 'Ieri',
                'week' => 'Ultimi 7 giorni',
                'older' => 'Più vecchie',
            ],
        ],
    ],

    'runs' => [
        'label' => 'Esecuzione di ingestion',
        'plural' => 'Esecuzioni di ingestion',
    ],

    'documents' => [
        'label' => 'Documento',
        'plural' => 'Documenti',
    ],

    'queries' => [
        'label' => 'Interrogazione',
        'plural' => 'Interrogazioni',
    ],
];
