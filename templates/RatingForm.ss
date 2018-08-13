<% if $IncludeFormTag %>
<form $AttributesHTML>
<% end_if %>
	<% if $Message %>
	<p id="{$FormName}_error" class="message $MessageType">$Message</p>
	<% else %>
	<p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
	<% end_if %>

	<fieldset>
		<% if $Legend %><legend>$Legend</legend><% end_if %>
		
		<h4 class="rating-title">$Title</h4>

		<div class="rating-intro <% if $Submitted %>rating-intro--success<% end_if %>">$Intro</div>

		<% if $IncludeRating %>
			<div class="rating-stars">
				$Fields.fieldByName('Rating').FieldHolder
			</div>
		<% end_if %>

		<% if not $Submitted %>
		<div class="rating-comment-wrapper">
			<% if $IncludeFeedback %>
				<div class="rating-comment">
					$Fields.fieldByName('Comments').FieldHolder
				</div>
			<% end_if %>
			<div class="rating-captcha">
				$Fields.fieldByName('Captcha').FieldHolder
			</div>
			<% if $Actions && not $Submitted %>
				<div class="Actions">
					<% loop $Actions %>
						$Field
					<% end_loop %>
				</div>
			<% end_if %>
		</div>
		<% else %>
			<% if $SubmittedComments %>
				<div class="rating-comment rating-comment--submitted">
					<p>$SubmittedComments</p>
				</div>
			<% end_if %>
		<% end_if %>

		<div class="clear"><!-- --></div>
	</fieldset>
	
<% if $IncludeFormTag %>
</form>
<% end_if %>
