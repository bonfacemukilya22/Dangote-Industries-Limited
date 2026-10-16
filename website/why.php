<?php
require __DIR__ . '/inc.php';
page_head('Why Choose Us', 'why.php');

// ================= EDIT YOUR CONTENT HERE =================
$heroTitle = 'Why choose us';
$heroText  = 'We offer a dynamic work environment where you can develop your skills and contribute to our growth.';
$heroImage = 'images/wwa.jpg.png';

$splitImage = 'images/work.jpg.png';
$splitTitle = 'Empowerment through progress';
$splitText  = 'Write a short paragraph here about your company culture and how your team works together.';

$workTitle = 'Working at Dangote';

// Light blue headings: [heading, paragraph]
$values = [
  ['Positioned for impact.',       'Meeting the basic needs of people across the world is at the core of what we do at the Dangote Group. We are building a team consisting of the best minds from all over the world doing their best work to bring about this outcome. We have created an environment where employees are valued, empowered, and can thrive both professionally and personally.'],
  ['Borderless opportunities.',    'With presence in over 10 countries, 7 business units and 30,000+ employees, opportunities for career growth within the group across the nations are endless. We leverage our many talent management initiatives to help our people learn, develop and grow to their full potential.'],
  ['Employee support and wellbeing.', 'We provide support to employees and promote their wellbeing through various initiatives designed to enhance their physical, mental, and emotional health.'],
  ['Diversity.',                   'At Dangote, our culturally diverse teams commit to Individuality, Teamwork and Leadership that builds respect, dignity, fairness, and duty of care, enabling all employees to develop their potential in an inclusive environment.'],
  ['Career pathway for graduates.','As a young graduate, kickstarting their career journey, join our Dangote Graduate Trainee Program for insights into different industries and sectors courtesy of our diverse businesses. You will receive structured training that will deepen your knowledge and enhance your career in whatever area you choose to major. You will enjoy on-the-job learning, participate in hands-on projects and receive support from supervisors trained to handhold you to success. As you journey to become a future leader.'],
  ['Opportunities for highly skilled professionals.', 'As an experienced professional at Dangote with advanced skills and expertise, you will constantly unlock your full potential and achieve your career goals. Whether you are seeking new challenges or opportunities for career development, we provide valuable insights, numerous resources, and support services to fulfil your career aspirations. Join our seasoned professionals today and take the next step towards enjoying a fulfilling career.'],
];

// Bold blue headings: [heading, paragraph]. Add 'plain' as a third item for a black heading.
$benefits = [
  ['Training & Development', 'At Dangote Group, we are changing the face of Learning & Development and putting training in your hands by leveraging technology to drive all training activities. We have a robust library of quality training programmes and materials, which are available and accessible to you.'],
  ['Inter-business Mobility', 'As one of the largest conglomerates in sub-Saharan Africa, we believe in job rotation through roles allows the employee to accumulate a wealth of skills which becomes extremely useful to both the employee and the company.'],
  ['Leave Allowance',         'Enjoy flexibility to care for you and your family, recuperate, further your education, plan for your future, and attend to other personal matters. We ensure that all staff members take time off at appropriate times.'],
  ['Health Insurance',        'We recognize the fundamental importance of efficient healthcare and safety in sustaining productivity. We help look after your health and wellbeing, enabling you to deliver your best at home and at work. We provide healthcare services to all staff.'],
  ['Commuter Benefit',        'We provide safe and reliable transportation options for employees.'],
  ['Retirement Plans',        'We participate in the compulsory contributory pension fund scheme as set up by PENCOM under the Pension Reform Act 2014. Deductions are made via payroll and remitted directly to your PFA. Upon employment, you automatically join the scheme.', 'plain'],
];
// ==========================================================
?>
<div class="ab-hero flat">
  <div class="ab-text"><h1><?= e($heroTitle) ?></h1><p><?= e($heroText) ?></p></div>
  <img class="ab-side" src="<?= e($heroImage) ?>" alt="">
</div>

<div class="ab-split">
  <div class="ab-pic" style="background-image:url('<?= e($splitImage) ?>')"></div>
  <div class="ab-copy"><h2><?= e($splitTitle) ?></h2><p><?= nl2br(e($splitText)) ?></p></div>
</div>

<div class="wk">
  <h2><?= e($workTitle) ?></h2>
  <?php foreach ($values as $v): ?>
    <h3><?= e($v[0]) ?></h3>
    <p><?= e($v[1]) ?></p>
  <?php endforeach; ?>

  <?php foreach ($benefits as $b): ?>
    <h3 class="strong<?= isset($b[2]) ? ' plain' : '' ?>"><?= e($b[0]) ?></h3>
    <p><?= e($b[1]) ?></p>
  <?php endforeach; ?>
</div>
<?php page_foot();