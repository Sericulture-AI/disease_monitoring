# Disease Monitoring Mechanism — CMER&TI (muga & eri)

Extracted from `cmerti-site` (pages listed per stage). Figures are as published on those pages; nothing added.

Infection travels by four routes — diseased/dead silkworms, their faecal matter, contaminated leaf, contaminated appliances. Every measure below closes one of those four. Muga (outdoor) does it by preparation and sanitation; eri (indoor) largely by building integrity.

```
 1 FOREWARN ──► 2 EXCLUDE AT SOURCE ──► 3 WATCH THE CROP ──► 4 DIAGNOSE ──► 5 CONTROL & DISINFECT
     ▲                                                                              │
     └──────────── field incidence + actual weather feed the next calendar ─────────┘
```

---

## Stage 1 — Forewarn (`pages/training/forewarning.php`, `pages/research/muga-calendar.php`)

Principle: every disease/pest has a peak season; control must be applied **before** the peak. The same spray two weeks earlier controls most of what a late spray misses.

Four forewarning calendars (images, not yet copied — see Open issues):

| Calendar | Targets | Control measure (from page) |
|---|---|---|
| Som diseases | Leaf spot, anthracnose, grey blight | Leaf spot: pruning/pollarding; 0.1 % Dithane M-45 (1 g/L) ×2 at 15 d before peak → up to 96 % control. Anthracnose: pluck & burn; 0.1 % Bavistin (1 g/L) ×2 at 15 d before peak → up to 96 %. Grey blight: pruning/pollarding; Bavistin ×2 at 15 d → 85.2 % (strength unconfirmed) |
| Host-plant pest — stem borer | Stem borer, leaf gall | Stem borer: 5–15 % extract of neem/dhatura/titabahak/castor; plug holes with 1.5 % Nuvan swab + mud plaster. Leaf gall: collect & destroy; >25 % infestation → prune and burn |
| Silkworm disease | Muscardine, flacherie | Muscardine: sunny plot, disinfect field, nylon net, LAHDOI on leaves/trunk/soil **7 days before Jarua brushing**. Flacherie: disinfect field, proper pruning schedule, age-specific leaf |
| Uzi fly | *Exorista* spp. | Netting and trapping in place before fly build-up |

Timing reference — muga crop calendar:

| Crop | Season | Category | Brushing | Larval period |
|---|---|---|---|---|
| Jarua | Dec–Jan | Pre-seed | 3rd wk Dec | 50–55 d |
| Chotua | Feb–Mar | Seed | 2nd wk Feb | 40–45 d |
| Jethua | Apr–May | Commercial | 3rd wk Apr | 24–30 d |
| Aherua | Jun–Jul | Pre-seed | 4th wk Jun | 22–28 d |
| Bhodia | Aug–Sep | Seed | 2nd wk Aug | 25–31 d |
| Kotia | Oct–Nov | Commercial | 1st wk Oct | 26–32 d |

Weather trigger (muscardine): low temperature + high humidity. Lahdoigarh Dec 2012 (min 5.5 °C, RH 72–92 %) → epidemic in Jarua; Dec 2016 (min 10.0 °C, RH 44–82 %) → nil. Heavy rainfall has moved risk into Chotua and early Jethua → **read the calendar against actual rainfall, not the month alone.** A Jarua failure is felt two crops later in Jethua.

Downloadable calendars referenced (not yet on site): Revised Forewarning Calendar 2016; Disease calendar 2015-16 (district-wise); Disease calendar quarter ending Sep 2014.

### Month-wise risk matrix (built from seasons stated on the pages)

`██` peak/main season · `░░` present/watch · blank = not stated

| Disease / pest | Host | J | F | M | A | M | J | J | A | S | O | N | D |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Leaf spot | Som | ░ | ░ | ░ | ░ | ░ | █ | █ | █ | ░ | ░ | ░ | ░ |
| Anthracnose | Som | | | | | | | █ | | | | | |
| Grey blight | Som | | | | | █ | █ | █ | █ | | | | |
| Brown blight | Soalu | | | | | | | | | █ | █ | | |
| Grey blight | Soalu | | █ | █ | | | | | | | █ | █ | █ |
| Seedling blight | Castor | | | | | | ░ | █ | █ | █ | | | |
| Alternaria blight | Castor | | | | █ | █ | █ | █ | | | | | |
| Cercospora leaf spot | Castor | | | | | █ | █ | █ | █ | | | | |
| Powdery mildew | Castor | █ | █ | █ | | | | | | | | █ | █ |
| Muscardine | Muga | █ | ░ | ░ | ░ | | | | | | | | █ |
| Uzi fly | Muga | ░ | ░ | █ | █ | | | | | | | ░ | ░ |
| Pebrine | Eri | ░ | ░ | ░ | ░ | ░ | ░ | ░ | ░ | ░ | ░ | ░ | ░ |
| Flacherie | Eri | ░ | ░ | ░ | ░ | ░ | ░ | ░ | ░ | ░ | ░ | ░ | ░ |

Season-only (no months stated): castor Fusarium wilt (rainy season); eri muscardine (winter); eri grasserie (rainy & summer); eri uzi (year-round, peak in rains); stem borer (calendar image).

---

## Stage 2 — Exclude at source (`muga-grainage.php`, `eri-grainage.php`, `eri-chawki.php`)

Grainage is where disease is excluded or passed on. Pebrine is transmitted vertically (transovarial, transovum) and horizontally (appliances, kharika). Cocoons infected in the 5th instar look sound → **appearance never substitutes for mother moth examination (MME).**

Grainage hygiene (muga): hall disinfected with 5 % bleaching powder one week ahead, cleared of ants/lizards/uzi maggots, sealed 2–3 days; reject deformed, darkened or weak moths; laying limited to 3 days; eggs surface-sterilised in 2 % formaldehyde, then soap and fresh water.

**Mother moth examination (two persons, cross-checking):**

1. Macerate one moth in 10 ml K₂CO₃ — 0.8–1.0 % (muga), 0.6–0.8 % (eri).
2. Settle 3–5 min in a (plastic) tube.
3. Filter through 2–3 layers of absorbent cotton; centrifuge 5 min at 3,000 rpm.
4. Decant; disperse sediment in 0.8 % (muga) / 0.6 % (eri) K₂CO₃ or 2 % KOH.
5. Read two smears × five fields at 600×; record spores per field.

Eri grainage schedule: disinfect building & appliances 2 days before procurement → cocoon/pupal testing (day 7) → … → MME, egg washing & drying (day 18 onwards) → packing → incubation. Disinfect after every cycle.

Eri chawki centre certification (despatch at 2nd moult): missing larvae < 2 %; undersized < 2 %; growth 5.50–6.00 g/100 larvae; **pebrine spores detected → reject and burn the batch, disinfect the centre immediately.** Each certificate records centre, brushing date, hatching %, DFL source/lot, breed, number of DFLs.

---

## Stage 3 — Watch the crop (`muga-rearing.php`, `eri-rearing.php`)

**Muga (outdoor):** plot shady in summer, sunny in winter; drain stagnant water; clean trees 5–10 days ahead; disinfect appliances in 5 % bleaching powder and sun-dry; trunk banding; bamboo forceps for dead/diseased larvae; 5 % bleaching powder solution at site for hands; bury diseased, dead and undersized larvae in a 1.5 ft pit away from the field, dusted with 9:1 slaked lime : bleaching powder; net and smoke management by season.

**Eri (indoor) disinfection schedule:**

| When | Activity |
|---|---|
| On completion of rearing | 1st disinfection — 0.05 % Asthra or 2 % bleaching powder |
| 3 days before | Clean and wash building and appliances |
| 2 days before | 2nd disinfection — same agents |
| 1 day before | Dust 5 % bleaching powder in slaked lime around the house; ventilate |

Lime dusting before first feed each instar and at each moult; 5th instar days 2, 4, 6. Mesh, self-closing doors, ante-chamber, ant wells. Never dispose of litter in the open.

---

## Stage 4 — Diagnose (`crop-protection.php`, `silkworm-diseases.php`, `host-plant-diseases.php`, `pests-predators.php`)

### Muga silkworm

| Disease | Organism | Key symptoms | Management |
|---|---|---|---|
| Pebrine (*phutuka*) | *Nosema* sp. AA1 | Irregular growth/moult; black specks on late larvae; deformed moths | MME-certified surface-sterilised DFLs; 5 % bleaching powder or 0.02 % NaOCl for appliances; 9:1 lime : bleaching powder dusting |
| White muscardine | *Beauveria bassiana* | Pale, sluggish; dark intersegments; death 3–5 d; white powder | Field disinfection; stage-specific leaf; 0.1 % LAHDOI 7 d before brushing, then every 15 d |
| Black/green muscardine | *Metarrhizium* / *Nomuraea* | As white, different conidial colour | As above |
| Aspergillosis | *A. flavus, parasiticus, tamarii* | Early instars die in 2–3 d, later in 4–6 d | As above; early-age hygiene |
| Grasserie | NPV | Swollen, loss of clasping, turbid white fluid | 0.2 % NaOCl egg disinfection; remove & destroy; lime dusting; bury |
| Bacterial flacherie | Bacteria | Pale, vomiting gut juice, semi-solid faeces | As grasserie |
| Infectious flacherie | Bacteria + IFV/DNV | Diarrhoea, black haemolymph, chain excreta, swollen anus | As grasserie; healthy leaf only |

Muscardine can take 60–90 % of Jarua. >50 % of primarily pebrine-infected larvae die before 3rd moult.

### Eri silkworm

| Disease | Organism | Season | Symptoms / management |
|---|---|---|---|
| Pebrine | *N. philosomiae*, *N. ricini* | Year-round | Poor laying, irregular hatch; MME, certified DFLs, egg sterilisation |
| Muscardine | *Beauveria*, *Metarhizium* | Winter | Mummification; disinfection, bed drying with lime, remove infected |
| Grasserie | SariNPV / PcNPV | Rains & summer | Late 5th instar bloating, yellow skin; lime, sanitation, no overcrowding, no tender leaf late |
| Flacherie | Viral + secondary bacterial | Year-round, worst summer/rains | Leaf quality, bed hygiene, stocking discipline |

Grasserie (wet, late-age) and muscardine (cold) sit at opposite ends — one disinfection regime does not replace season-specific bed management.

### Host plants

| Host | Disease | Organism | Peak | Leaf loss (kg/ha/yr) | Control |
|---|---|---|---|---|---|
| Som | Leaf spot | *Phyllosticta perseae* | Jun–Aug | 3,214 | 0.1 % Indofil M-45 ×2 at 15 d before peak; S3 som |
| Som | Anthracnose | *Colletotrichum gloeosporioides* | July | 2,932 | Pluck & burn; Indofil M-45 prophylactic |
| Som | Grey blight | *Pestalotiopsis desiminata* | May–Aug | 3,705 | 0.1 % Bavistin ×2 at 15 d |
| Som | Red rust | *Cephaleuros parasiticus* | — | 3,022 | Prune; 1 % Bordeaux |
| Soalu | Brown blight | *C. gloeosporioides* | Sep–Oct | 3,142 | Copper oxychloride 3 g/L at 15 d |
| Soalu | Grey blight | *Pestalotiopsis* spp. | Feb–Mar, Oct–Dec | 3,801 | Copper oxychloride 3 g/L |
| Castor | Seedling blight | *Phytophthora parasiticus* | end Jun–Sep | — | Fytolan/Blitox-50 3 g/L |
| Castor | Alternaria blight | *Alternaria ricini* | Apr–Jul | — | Fytolan/Blitox-50 3 g/L |
| Castor | Cercospora | *Cercospora ricinella* | May–Aug | — | Dithane Z-78 / Ziram 2 g/L |
| Castor | Powdery mildew | *Leveillula taurica* | Nov–Mar | — | 0.2 % wettable sulphur |
| Castor | Fusarium wilt | *F. oxysporum* f. sp. *ricini* | Rains | — | Thiram 3 g/kg or Carbendazim 2 g/kg seed; Carbendazim drench |

Safety period before feeding: 7 days after Fytolan/Blitox-50; 1 month after wettable sulphur. Som foliar disease ≈ 12,900 kg leaf/ha/yr against 18,000–20,000 kg yield.

### Pests & predators

Uzi fly (*Exorista sorbillans*, also *Blepharipa zebina*): Nov–Apr, worst Chotua (Mar–Apr), 20–40 % loss. Net first; destroy infested larvae/cocoons, never carry suspect cocoons into grainage; release *Nesolynx thymus* (also *Exoristobia philippinensis*); chemical control effectively closed (muga is pesticide-sensitive). Biocontrol package (2009): +30 % cocoon yield, B:C 2.03:1. Other enemies: *Apanteles*, *Xanthopimpla*, stink bug, assassin bug, weaver/carpenter ants, *Vespa*, mantis, birds. Eri: uzi, ants, cockroach, rat, lizard — mesh, ant wells, traps. A muga field and an eri house in one locality share one uzi population.

---

## Stage 5 — Control & disinfect (`disinfection.php`)

| Agent | Working strength | Notes |
|---|---|---|
| Sodium hypochlorite | 0.02–0.05 % field; 0.2 % eggs | Broad spectrum; routine in-crop |
| Formalin | 2 % (1 : 17 water) | Sealed room, >20–25 °C, 70 % RH; carcinogenic — full PPE mandatory |
| Bleaching powder | 2 % in 0.3 % lime (spray); 5 % appliances | Needs 30 % active chlorine; wet surfaces only |
| Slaked lime | 0.3 %; 9 : 1 with bleaching powder for dusting | Hygiene, bed humidity |
| LAHDOI | 1 g/L | Anti-muscardine; 7 d before brushing then every 15 d; ~70 % prevention; B:C 1 : 3.5; use within 6 months |
| Chlorine dioxide | 2–5 % + 0.5 % lime | Sanitech/Serichlor 20,000 ppm, activate at point of use |

Preparation: 2 % formalin = volume ÷ 18 → formalin, +17 parts water. 100 L of 2 % bleaching powder in 0.3 % lime = 2 kg BP + 0.3 kg lime paste, stand 10 min, use supernatant. 100 L 0.3 % lime = 300 g. 100 L 2.5 % ClO₂ = 250 g activator + 2.5 L Sanitech/Serichlor, 10 min, into 97.5 L water with 500 g lime. Appliance dip ≥ 10 min in 2 % BP / 0.3 % lime; 6×3 ft tank at 2 ft ≈ 1,008 L; 10 dips per batch; sun-dry odour-free.

---

## Programme layer (`data/disease-projects.json`)

- **ARP 5023 CN** — Muga and Eri silkworm disease monitoring and management in NE states (Mar 2023 – Feb 2028), ongoing.
- **Regular programme** — Forecasting and forewarning for pest and diseases of muga host plants and silkworm (since Apr 2014; predecessor PRE 5852, Jan 2011 – Dec 2013).
- DSS for early warning of muga diseases & pests with geospatial technique (2015-16 → completed 2019-20).
- Foldscope for pebrine detection (completed 2019-20); AI pebrine identification (2024 paper); 2026-27 trial: pebrine detection via mobile/web app; NaOCl & Nirmool egg disinfection validation.
- Ongoing: SRP 05036 MIC (*Nosema* virulent/avirulent strains, 2025-28); SRE 05031 SNC (infochemicals for uzi, 2025-28). New: SRP 05042 SIC (botanical formulations for muscardine, 2026-29).

Full list: 20 projects, 30 publications in the JSON.

---

## Open issues carried over from the source pages

1. **Grey blight dose unconfirmed** — old page gave "0.1 % (1 g/10 L)", which is inconsistent (1 g/10 L = 0.01 %). Host Plant Diseases page states 0.1 % Bavistin. Confirm with Pathology.
2. **Anthracnose chemical differs** — forewarning page: 0.1 % Bavistin; host plant page: Indofil M-45 prophylactic.
3. **Calendar images missing** — `forewarning-som.jpg`, `forewarning-stem-borer.jpg`, `forewarning-silkworm.jpg`, `forewarning-uzi.jpg` → drop into `images/services/`.
4. **Calendar PDFs missing** — `forewarning-calendar-2016-revised.pdf`, `forewarning-calendar-2015-16.pdf`, `disease-calendar-2014-q3.pdf` → `documents/services/`.
5. **Latest calendar is 2016** — replace if Pathology has issued a newer one.
