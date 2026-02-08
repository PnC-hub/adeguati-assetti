export interface BlogArticle {
  slug: string
  title: string
  excerpt: string
  content: string
  date: string
  readTime: string
  category: string
  keywords: string[]
}

export function useBlogArticles() {
  const articles: BlogArticle[] = [
    {
      slug: 'art-2086-codice-civile-guida-completa',
      title: 'Art. 2086 Codice Civile: Guida Completa per Imprenditori',
      excerpt: 'Tutto quello che devi sapere sugli obblighi di legge per gli adeguati assetti organizzativi, amministrativi e contabili.',
      content: `
        <h2>Cos'e l'Art. 2086 del Codice Civile?</h2>
        <p>L'articolo 2086 del Codice Civile, modificato dal D.Lgs. 14/2019 (Codice della Crisi), impone all'imprenditore che opera in forma societaria o collettiva il dovere di istituire un assetto organizzativo, amministrativo e contabile adeguato alla natura e alle dimensioni dell'impresa.</p>

        <h2>Gli obblighi specifici</h2>
        <p>L'imprenditore deve:</p>
        <ul>
          <li>Istituire un assetto organizzativo adeguato</li>
          <li>Predisporre un assetto amministrativo idoneo</li>
          <li>Implementare un sistema contabile appropriato</li>
          <li>Rilevare tempestivamente la crisi d'impresa</li>
        </ul>

        <h2>Conseguenze del mancato adempimento</h2>
        <p>Il mancato adeguamento puo comportare:</p>
        <ul>
          <li>Responsabilita personale dell'amministratore</li>
          <li>Sanzioni fino a 50.000 euro</li>
          <li>Ispezione giudiziaria ex Art. 2409 c.c.</li>
        </ul>
      `,
      date: '2026-01-15',
      readTime: '8 min',
      category: 'Normativa',
      keywords: ['art 2086', 'codice civile', 'adeguati assetti', 'compliance aziendale']
    },
    {
      slug: 'dscr-calcolo-interpretazione',
      title: 'DSCR: Come Calcolarlo e Interpretarlo Correttamente',
      excerpt: 'Il Debt Service Coverage Ratio e uno degli indicatori chiave per valutare la capacita di un\'azienda di far fronte ai propri debiti.',
      content: `
        <h2>Cos'e il DSCR?</h2>
        <p>Il DSCR (Debt Service Coverage Ratio) misura la capacita di un'azienda di coprire il servizio del debito con i flussi di cassa operativi. E' uno dei 7 KPI obbligatori secondo gli indici CNDCEC.</p>

        <h2>Formula di calcolo</h2>
        <p>DSCR = Flusso di cassa operativo / Servizio del debito</p>

        <h2>Come interpretare il risultato</h2>
        <ul>
          <li><strong>DSCR > 1.2:</strong> Situazione positiva</li>
          <li><strong>DSCR tra 1 e 1.2:</strong> Attenzione richiesta</li>
          <li><strong>DSCR < 1:</strong> Situazione critica</li>
        </ul>
      `,
      date: '2026-01-20',
      readTime: '5 min',
      category: 'KPI',
      keywords: ['dscr', 'debt service coverage ratio', 'indicatori crisi', 'kpi finanziari']
    },
    {
      slug: 'responsabilita-amministratore-srl',
      title: 'Responsabilita Amministratore SRL: Cosa Rischi Davvero',
      excerpt: 'L\'amministratore di una SRL risponde personalmente per la mancata implementazione degli adeguati assetti. Ecco cosa devi sapere.',
      content: `
        <h2>La responsabilita personale dell'amministratore</h2>
        <p>Con la riforma del Codice della Crisi, l'amministratore di SRL ha assunto nuovi e gravosi obblighi. Il mancato rispetto puo comportare responsabilita patrimoniale personale.</p>

        <h2>Casi reali di condanna</h2>
        <p>Il Tribunale di Catanzaro (Decreto n. 6/2024) ha disposto l'ispezione giudiziaria di una societa per mancata implementazione degli assetti adeguati.</p>

        <h2>Come proteggersi</h2>
        <ul>
          <li>Implementare un sistema di monitoraggio KPI</li>
          <li>Documentare tutte le decisioni</li>
          <li>Verificare periodicamente la compliance</li>
        </ul>
      `,
      date: '2026-01-25',
      readTime: '6 min',
      category: 'Legale',
      keywords: ['responsabilita amministratore', 'srl', 'sanzioni', 'patrimonio personale']
    },
    {
      slug: 'indicatori-crisi-cndcec-7-kpi',
      title: 'Indicatori Crisi CNDCEC: I 7 KPI Obbligatori',
      excerpt: 'Il CNDCEC ha definito 7 indicatori fondamentali per la rilevazione tempestiva della crisi. Ecco quali sono e come monitorarli.',
      content: `
        <h2>I 7 KPI secondo il CNDCEC</h2>
        <ol>
          <li>Patrimonio netto negativo</li>
          <li>DSCR a 6 mesi</li>
          <li>Indice di liquidita</li>
          <li>Indice di indebitamento</li>
          <li>Sostenibilita degli oneri finanziari</li>
          <li>Adeguatezza patrimoniale</li>
          <li>Ritardi nei pagamenti</li>
        </ol>

        <h2>Soglie di allerta</h2>
        <p>Ogni indicatore ha soglie specifiche definite dal CNDCEC che variano in base al codice ATECO dell'azienda.</p>
      `,
      date: '2026-01-28',
      readTime: '7 min',
      category: 'KPI',
      keywords: ['cndcec', 'indicatori crisi', '7 kpi', 'soglie allerta']
    },
    {
      slug: 'tribunale-catanzaro-2024-sentenza',
      title: 'Decreto Tribunale Catanzaro 2024: Cosa Significa per le Imprese',
      excerpt: 'L\'importante decreto del Tribunale di Catanzaro di febbraio 2024 ha segnato un precedente fondamentale per la compliance aziendale.',
      content: `
        <h2>Il Decreto n. 6/2024</h2>
        <p>Il Tribunale di Catanzaro, Sezione Imprese, con decreto del febbraio 2024, ha disposto l'ispezione giudiziaria ex Art. 2409 c.c. per verificare l'adeguatezza degli assetti organizzativi.</p>

        <h2>I 13 elementi verificati</h2>
        <p>Il giudice ha ordinato la verifica di specifici elementi tra cui:</p>
        <ul>
          <li>Organigramma aziendale</li>
          <li>Mansionario</li>
          <li>Sistema di gestione rischi</li>
          <li>Budget e piani industriali</li>
          <li>Procedure di tesoreria</li>
        </ul>

        <h2>Implicazioni pratiche</h2>
        <p>Questo decreto rappresenta un precedente importante: dimostra che i Tribunali stanno effettivamente verificando la compliance all'Art. 2086.</p>
      `,
      date: '2026-02-01',
      readTime: '5 min',
      category: 'Giurisprudenza',
      keywords: ['tribunale catanzaro', 'decreto 2024', 'ispezione giudiziaria', 'art 2409']
    }
  ]

  const getArticle = (slug: string): BlogArticle | undefined => {
    return articles.find(a => a.slug === slug)
  }

  const getArticlesByCategory = (category: string): BlogArticle[] => {
    return articles.filter(a => a.category === category)
  }

  const getRecentArticles = (count: number = 3): BlogArticle[] => {
    return [...articles].sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()).slice(0, count)
  }

  return {
    articles,
    getArticle,
    getArticlesByCategory,
    getRecentArticles
  }
}
