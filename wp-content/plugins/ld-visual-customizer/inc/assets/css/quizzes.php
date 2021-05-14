<style type="text/css">

    <?php
    $quiz_settings = array(
        'quiz_question_background'  =>  get_option('lds_quiz_bg'),
        'quiz_question_text'        =>  get_option('lds_quiz_txt'),
        'quiz_question_correct_text'        =>  get_option('lds_quiz_correct_txt'),
        'quiz_question_correct_background'  =>  get_option('lds_quiz_correct_bg'),
        'quiz_question_incorrect_text'      =>  get_option('lds_quiz_incorrect_txt'),
        'quiz_question_incorrect_background'    =>  get_option('lds_quiz_incorrect_bg'),
        'quiz_question_border'                  =>  get_option('lds_quiz_border'),
        'quiz_question_border_radius'           =>  strval( get_option('lds_quiz_border_radius') ),
    );

    if( ($quiz_settings['quiz_question_border'] && $quiz_settings['quiz_question_border'] != '') || ( $quiz_settings['quiz_question_border_radius'] && $quiz_settings['quiz_question_border_radius'] != '' ) ): ?>

          .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem textarea.wpProQuiz_questionEssay,
          .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionList[data-type=assessment_answer] .wpProQuiz_questionListItem,
          .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem>table,
          .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem>table td:first-child,
          .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem .wpProQuiz_sortable,
          .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label {
               <?php
               if( $quiz_settings['quiz_question_border'] && $quiz_settings['quiz_question_border'] != '' ): ?>
                    border-color: <?php echo $quiz_settings['quiz_question_border']; ?> !important;
               <?php endif;
               if( $quiz_settings['quiz_question_border_radius'] && $quiz_settings['quiz_question_border_radius'] != '' ): ?>
                    border-radius: <?php echo $quiz_settings['quiz_question_border_radius']; ?>px;
               <?php
               endif; ?>
          }

    <?php
    endif;
    if( $quiz_settings['quiz_question_correct_background'] && $quiz_settings['quiz_question_correct_background'] != '' ): ?>

        .learndash-wrapper .wpProQuiz_content .wpProQuiz_response {
             background: transparent;
             padding: 0;
             min-height: inherit;
             border: 0;
        }

        .learndash-wrapper .wpProQuiz_correct {
            border: 0 !important;
            padding: 25px;
           border-radius: 6px;
            background-color: <?php echo $quiz_settings['quiz_question_correct_background']; ?> !important
        }

    <?php
    endif;
    if( $quiz_settings['quiz_question_correct_text'] && $quiz_settings['quiz_question_correct_text'] != '' ): ?>

        .learndash-wrapper .wpProQuiz_correct > .wpProQuiz_response,
        .learndash-wrapper .wpProQuiz_correct {
            color: <?php echo $quiz_settings['quiz_question_correct_text']; ?> !important
        }

    <?php
    endif;

    if( $quiz_settings['quiz_question_incorrect_background'] && $quiz_settings['quiz_question_incorrect_background'] != '' ): ?>
         .learndash-wrapper .wpProQuiz_content .wpProQuiz_response {
              background: transparent;
              padding: 0;
              min-height: inherit;
              border: 0;
         }

        .learndash-wrapper .wpProQuiz_incorrect {
            border: 0 !important;
            padding: 25px;
            border-radius: 6px;
            background-color: <?php echo $quiz_settings['quiz_question_incorrect_background']; ?>  !important;
        }

    <?php
    endif;
    if( $quiz_settings['quiz_question_incorrect_text'] && $quiz_settings['quiz_question_incorrect_text'] != '' ): ?>
        .learndash-wrapper .wpProQuiz_incorrect > .wpProQuiz_response,
        .learndash-wrapper .wpProQuiz_incorrect {
            color: <?php echo $quiz_settings['quiz_question_incorrect_text']; ?> !important;
        }
    <?php
    endif;

    if( $quiz_settings['quiz_question_background'] && $quiz_settings['quiz_question_background'] != '' ): ?>
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem textarea.wpProQuiz_questionEssay,
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionList[data-type=assessment_answer] .wpProQuiz_questionListItem,
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem .wpProQuiz_sortable,
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label {
            background-color: <?php echo $quiz_settings['quiz_question_background']; ?> !important
        }
    <?php
    endif;
    if( $quiz_settings['quiz_question_text'] && $quiz_settings['quiz_question_text'] != '' ): ?>
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem textarea.wpProQuiz_questionEssay,
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionList[data-type=assessment_answer] .wpProQuiz_questionListItem,
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem .wpProQuiz_sortable,
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label {
            color: <?php echo $quiz_settings['quiz_question_text']; ?> !important
        }
    <?php
    endif; ?>

</style>
