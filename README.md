<h1>CSV Object Importer Task</h1>

<p>Your task is to create a small PHP powered tool which can take a CSV file as an input, parse the
columns and rows into an object, sort the objects, ensure the Transaction Code is valid and then
return the objects in a table format.</p>
<p>Please use the CSV file attached to the email.</p>

<div style="border: solid; padding: 10px;">
<b>The object: BankTransaction</b>

<p><b>Attributes:</b>
<ul>
<li>Date (DateTime)</li>
<li>Transaction Code (String)</li>
<li>Customer Number (Int)</li>
<li>Reference (String)</li>
<li>Amount (Currency) saved in cents</li>
</ul></p>
</div>

<p><b>Please include:</b>
<ul>
<li>Use Object Oriented PHP for your solution. There is no need to save anything to a database.</li>
<li>The sort should be by the Date and Time.</li>
<li>You should ensure the figures from the CSV are parsed as the correct type above</li>
<li><b>The Transaction Code can be verified as correct by using the algorithm provided along this document. The table should display if the transaction code is valid or not.</b></li>
</ul></p>

<p><b>Things to note:</b>
<ul>
<li>You can assume the CSV will always be in the correct format</li>
<li>Your code will need to decide if it’s a debit or credit per the amount</li>
</ul></p>

<p><b>Submission requirements:</b>
<ul>
<li>Provide a small explanation outlining reasons why you selected your approach and why you
decided against any other solutions.</li>
<li>You must use PHP 7+ for your implementation</li>
<li>Ensure your code is appropriately documented and formatted using PSR-2 Style Guide
<a href="https://www.php-fig.org/psr/psr-2/">(https://www.php-fig.org/psr/psr-2/)</a></li>
</ul><p>