<div class="choice display_none" id="master_choice">
    <h3 class="back shad rad pad margin_rlb1">Выберите специалиста</h3>
        <div class="radio-group flex">
          <?php
          //вывод списка мастеров
            foreach ($masters as $master)
            {
              echo '
                <article class="main_section_article radio" data-value="' . $master[0] . '#' . $master[1] . '#' . $master[2] . '#' . $master[3] . '#' . $master[4] .'#' . $master[7] . '">
                  <div class="main_section_article_imgdiv" style="background-color: var(--bgcolor-content);">
                    <img src="about_imgs/' . $master[0] . '" alt="Фото ' . $master[2] . '" class="main_section_article_imgdiv_img" />
                  </div>

                    <div class="main_section_article_content">
                      <h3>' . $master[1] . ' ' . $master[2] . '</h3>
                      <span>
                        ' . $master[4].'
                      </span>
                    </div>
                </article>
              ';
            }
          ?>
          <input type="hidden" id="master" name="master" />
        </div>
</div>
<!-- master choice end -->
