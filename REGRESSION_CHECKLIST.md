# Mobile header regression checklist

Перевірити шапку (`body > .header`) після змін брейкпоінтів:

- [ ] **360px**: логотип має коректний `line-height`, navbar не має зсуву вліво (`margin-left: 0`), `.sidebar-toggle` клікабельний.
- [ ] **390px**: правий блок `.navbar-right` не накладається на `.sidebar-toggle`, пункти навігації видимі.
- [ ] **430px**: висота шапки достатня для логотипа і navbar, немає обрізання елементів по вертикалі.
- [ ] **768px**: перехід між мобільним і планшетним виглядом без "стрибка" layout, навігація залишається працездатною.

## Bootstrap 5.3 migration

- [ ] **Desktop (≥992px)**: dropdown профілю відкривається/закривається, а desktop-language switcher та кнопка Contact us мають правильну видимість.
- [ ] **Mobile (<992px)**: sidebar toggle, mobile search modal, collapse "Read books" і "Search books" працюють з клавіатури та дотиком.
- [ ] **Book/article pages**: вкладки переключають content panels, carousel controls працюють, а `panel`/`thumbnail` блоки не втратили межі, відступи або адаптивну сітку.
- [ ] **Modal content**: Ajax preview і Contact us коректно відкриваються, закриваються кнопкою/Escape/backdrop, та фокус повертається в тригер.
