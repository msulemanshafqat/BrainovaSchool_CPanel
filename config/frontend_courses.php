<?php

/**
 * Public "Courses" page content. Edit titles, copy, and categories here;
 * swap `image` URLs to your own assets when ready.
 */
return [
    'hero' => [
        'title'       => 'Explore our courses',
        'subtitle'    => 'Browse programs designed to build confidence, creativity, and future-ready skills—with clear paths for every age and level.',
        'primary_cta' => [
            'label' => 'Apply / enquire',
            'route' => 'frontend.contact',
        ],
        'secondary_cta' => [
            'label' => 'Online admission',
            'route' => 'frontend.online-admission',
        ],
    ],

    'categories' => [
        ['slug' => 'all', 'label' => 'All programs'],
        ['slug' => 'stem', 'label' => 'STEM & computing'],
        ['slug' => 'math', 'label' => 'Mathematics'],
        ['slug' => 'language', 'label' => 'Languages'],
        ['slug' => 'skills', 'label' => 'Life skills'],
    ],

    'courses' => [
        [
            'category'    => 'stem',
            'badge'       => 'STEM',
            'title'       => 'Creative computing & robotics starter',
            'description' => 'Block-based coding, simple circuits, and team projects—ideal for first steps into logical thinking and design.',
            'age_range'   => 'Ages 7–10',
            'grade'       => 'Grade 3–5',
            'lessons'     => '48 lessons',
            'duration'    => '4–6 months',
            'enrolled'    => '500+ students',
            'accent'      => 'indigo',
            'image'       => null,
        ],
        [
            'category'    => 'stem',
            'badge'       => 'AI & code',
            'title'       => 'AI explorers: games & chatbots',
            'description' => 'Guided projects with safe AI tools—build mini-games, stories, and simple assistants while learning how models are used responsibly.',
            'age_range'   => 'Ages 10–14',
            'grade'       => 'Grade 6–8',
            'lessons'     => '72 lessons',
            'duration'    => '6–9 months',
            'enrolled'    => '320+ students',
            'accent'      => 'violet',
            'image'       => null,
        ],
        [
            'category'    => 'math',
            'badge'       => 'Math',
            'title'       => 'Math lab: problem-solving & reasoning',
            'description' => 'Concrete-to-abstract progression with puzzles, word problems, and collaborative challenges aligned to core numeracy goals.',
            'age_range'   => 'Ages 8–12',
            'grade'       => 'Grade 4–6',
            'lessons'     => '60 lessons',
            'duration'    => '6–8 months',
            'enrolled'    => '410+ students',
            'accent'      => 'teal',
            'image'       => null,
        ],
        [
            'category'    => 'math',
            'badge'       => 'Math+',
            'title'       => 'Competition math foundations',
            'description' => 'Pattern recognition, proofs at an introductory level, and timed strategy practice for aspiring Olympiad-track learners.',
            'age_range'   => 'Ages 11–15',
            'grade'       => 'Grade 7–9',
            'lessons'     => '54 lessons',
            'duration'    => '9 months',
            'enrolled'    => '180+ students',
            'accent'      => 'ocean',
            'image'       => null,
        ],
        [
            'category'    => 'language',
            'badge'       => 'Languages',
            'title'       => 'English fluency & academic writing',
            'description' => 'Reading comprehension, structured writing, vocabulary growth, and presentation skills with weekly feedback loops.',
            'age_range'   => 'Ages 9–14',
            'grade'       => 'Grade 5–8',
            'lessons'     => '72 lessons',
            'duration'    => 'Full year',
            'enrolled'    => '600+ students',
            'accent'      => 'amber',
            'image'       => null,
        ],
        [
            'category'    => 'language',
            'badge'       => 'World language',
            'title'       => 'Conversational second language circle',
            'description' => 'Small-group immersion, role-play, and culturally themed projects tailored to beginner and intermediate cohorts.',
            'age_range'   => 'Ages 10–16',
            'grade'       => 'Grade 6–10',
            'lessons'     => '40 lessons',
            'duration'    => 'Two terms',
            'enrolled'    => '260+ students',
            'accent'      => 'rose',
            'image'       => null,
        ],
        [
            'category'    => 'skills',
            'badge'       => 'Leadership',
            'title'       => 'Public speaking & debate studio',
            'description' => 'Storytelling, argument structure, constructive feedback, and low-stakes performance to build clarity and calm under pressure.',
            'age_range'   => 'Ages 11–16',
            'grade'       => 'Grade 6–10',
            'lessons'     => '36 lessons',
            'duration'    => '6 months',
            'enrolled'    => '140+ students',
            'accent'      => 'coral',
            'image'       => null,
        ],
        [
            'category'    => 'skills',
            'badge'       => 'Innovation',
            'title'       => 'Design thinking & entrepreneurship',
            'description' => 'Empathy interviews, prototyping, lightweight business models, and a capstone showcase for young founders.',
            'age_range'   => 'Ages 13–18',
            'grade'       => 'Grade 8–12',
            'lessons'     => '32 lessons',
            'duration'    => 'One term',
            'enrolled'    => '95+ students',
            'accent'      => 'slate',
            'image'       => null,
        ],
    ],

    'faqs' => [
        [
            'q' => 'How do I choose the right track?',
            'a' => 'Start with age band and weekly time commitment; our team can suggest a pairing after a short discussion or orientation session.',
        ],
        [
            'q' => 'Are programs online, on campus, or both?',
            'a' => 'Formats vary by term. Mention your preference when you enquire—we publish the current modality on each intake.',
        ],
        [
            'q' => 'Do you offer assessments or placements?',
            'a' => 'Yes. Lightweight diagnostics help match learners to the right starting unit without overwhelming newcomers.',
        ],
        [
            'q' => 'How do pricing and installments work?',
            'a' => 'Fees depend on programme length and format. Contact us or complete online admission notes for the latest fee sheet and installment options.',
        ],
    ],

    'trust' => [
        'headline' => 'Built for learners and families',
        'body'     => 'Structured progression, measurable outcomes, and regular communication so students stay motivated—and parents stay informed.',
    ],
];
