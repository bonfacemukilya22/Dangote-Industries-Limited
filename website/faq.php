<?php
require __DIR__ . '/inc.php';
page_head('Careers FAQ', 'faq.php');

// ================= EDIT YOUR CONTENT HERE =================
$heroImage = 'images/faq.jpg';   // your picture inside the images folder (change the file name to yours)
$intro = 'Our Frequently Asked Questions (FAQ) addresses common queries about careers at Dangote Group.';

// Each item: [question, answer]
// Add 'lead' as a third item for the small grey one-line style (like the first item in your design).
$faqs = [
  ['How do I apply for a job?', 'Visit our Careers page on www.yourcompany.com/careers', 'lead'],
  ['What types of positions are available?', 'We are a diverse conglomerate with a wide range of business functions to choose from. Please visit our website to learn more about the types of positions. You can also filter by location.'],
  ['Is there a graduate programme?', 'Yes. The Dangote Graduate Trainee Program provides insights into different industries and sectors courtesy of our diverse businesses. You will receive structured training that will deepen your knowledge and enhance your career in whatever area you choose to major. You will enjoy on-the-job learning, participate in hands-on projects and receive support from supervisors trained to handhold you to success as you journey to become a future leader.'],
  ['How does the company support professional development?', 'At Dangote Group, we are changing the face of Learning & Development. We have a robust training system through a range of blended learning systems; management development programmes, and core skills trainings for all employees. These programs and learning opportunities are aimed at helping employees grow in their careers.'],
  ['What is the dress code?', 'We have a policy regarding dress code and workplace attire. You will learn more about this during onboarding.'],
  ['Can I submit a general application?', 'Yes, candidates can express their interest in working at Dangote even when there are no specific job openings. Create a profile on the Careers page and turn on notifications to get updates on job openings across the Group.'],
  ['How can I check the status of my application?', 'Visit your Career profile on the Career Website for updates on the status of your application. You will also receive email notifications regarding the status of your application.'],
  ['What is the company\'s policy on diversity and inclusion?', 'We are committed to creating an inclusive culture where all employees feel heard, supported, and included. We value diversity in our workforce and strive to create an environment that respects and celebrates differences.'],
  ['Does the company offer internships?', 'Yes, we offer internship opportunities for students and recent graduates. Please visit our Careers page for more information on available internships and how to apply.'],
  ['What benefits does the company offer?', 'We offer a comprehensive benefits package that includes health insurance, retirement plans, paid time off, and other employee perks. Details about specific benefits can be found on our Careers page or during the onboarding process.'],
  ['Does Dangote Group consider candidates from diverse backgrounds?', 'Yes. As the largest conglomerate in Sub-Saharan Africa, we are committed to a diversified workforce and do consider candidates from diverse backgrounds globally.'],
  ];
// ==========================================================

// Escapes the text, then turns www.something.com into a clickable link
function faq_text($s) {
    return preg_replace('~(www\.[A-Za-z0-9./_-]*[A-Za-z0-9/_-])~', '<a href="https://$1" target="_blank" rel="noopener">$1</a>', e($s));
}
?>
<img class="faq-hero" src="<?= e($heroImage) ?>" alt="">

<div class="faq">
  <h1 class="sr"><?= e(t('faq')) ?></h1>
  <p class="faq-intro"><?= e($intro) ?></p>
  <?php foreach ($faqs as $f): ?>
    <?php if (isset($f[2])): ?>
      <p class="faq-lead"><b><?= e($f[0]) ?></b> <?= faq_text($f[1]) ?></p>
    <?php else: ?>
      <h3><?= e($f[0]) ?></h3>
      <p><?= nl2br(faq_text($f[1])) ?></p>
    <?php endif; ?>
  <?php endforeach; ?>
</div>
<?php page_foot();