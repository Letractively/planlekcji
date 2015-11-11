# Jak generowane są lekcje #
  * Każda sala ma przypisany przedmiot
  * Każdy nauczyciel ma przypisaną klasę

# Generowanie dostępnych zajęć #
**Zajęcie** - przypisany przedmiot do sali oraz nauczyciela do klasy jakiej aktualnie uczy

Zajęcia nauczyciel może prowadzić w sposób klasyczny (cała klasa) oraz w grupie

## Klasyczne zajęcia ##
Nauczyciel podczas jednej lekcji może uczyć tylko jedną klasę. Aktualnie zajęta klasa, jest automatycznie eliminowana podczas ustalania planu dla innych klas.

## Zajęcia w grupie ##
Nauczyciel może uczyć tylko jednego przedmiotu, tylko w jednej sali, ale może uczyć więcej niż jedną salę.

# Schemat #

| | nauczyciel | sala | przedmiot | klasa |
|:|:-----------|:-----|:----------|:------|
| klasyczne zajęcia | 1          | 1    | 1         | 1     |
| grupowe zajęcia | >1         | 1    | 1         | >1    |

# Tworzenie planu #
Najpierw należy utworzyć plan wspólny dla klasy (klasyczny), potem dla grupy (gdyż dla grupy, każde klasyczne zajęcie w danym dniu i dla danej lekcji jest blokowane). W przypadku istnienia planu grupowego, każda edycja planu zwykłego usnie przypisane zajęcia dla grup.